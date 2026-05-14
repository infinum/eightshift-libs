<?php

/**
 * The file that defines the autowiring process
 *
 * @package EightshiftLibs\Main
 */

declare(strict_types=1);

namespace EightshiftLibs\Main;

use EightshiftLibs\Exception\InvalidAutowireDependency;
use EightshiftLibs\Exception\NonPsr4CompliantClass;
use EightshiftLibs\Services\ServiceInterface;
use EightshiftLibs\Services\ServiceCliInterface;
use Exception;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use ReflectionNamedType;
// phpcs:ignore SlevomatCodingStandard.Namespaces.UnusedUses.UnusedUse
use ReflectionException;

/**
 * The file that defines the autowiring process
 */
class Autowiring
{
	/**
	 * Constructor parameter names that are passed through from Main itself and must
	 * be ignored when the autowire scanner encounters them as primitive dependencies.
	 *
	 * Classes whose constructors contain ONLY ignored primitives (e.g. Main) are
	 * intentionally not registered in the dependency tree.
	 */
	private const IGNORED_PRIMITIVE_PARAMS = [
		'psr4Prefixes' => true,
		'namespace' => true,
		'projectNamespace' => true,
	];

	/**
	 * Initialize the autowiring scanner with the composer PSR-4 map and the target project namespace.
	 *
	 * @param array<string, mixed> $psr4Prefixes Composer's ClassLoader psr4Prefixes. $ClassLoader->getPsr4Prefixes().
	 * @param string $namespace Project namespace.
	 */
	public function __construct(
		protected readonly array $psr4Prefixes,
		protected readonly string $namespace,
	) {
	}

	/**
	 * Autowiring.
	 *
	 * @param array<string, mixed> $manuallyDefinedDependencies Manually defined dependencies from Main.
	 * @param bool $skipInvalid Skip invalid namespaces rather than throwing an exception. Used for tests.
	 *
	 * @throws Exception Exception thrown in case class is missing.
	 *
	 * @return  array<string, mixed> Array of fully qualified class names.
	 */
	public function buildServiceClasses(array $manuallyDefinedDependencies = [], bool $skipInvalid = false): array
	{
		$projectReflectionClasses = $this->validateAndBuildClasses(
			$this->filterManuallyDefinedDependencies(
				$this->getClassesInNamespace($this->namespace, $this->psr4Prefixes),
				$manuallyDefinedDependencies
			),
			$skipInvalid
		);

		$dependencyTree = [];
		$reflectionCache = $projectReflectionClasses;

		// Prepare the filename index.
		$filenameIndex = $this->buildFilenameIndex($projectReflectionClasses);
		$classInterfaceIndex = $this->buildClassInterfaceIndex($projectReflectionClasses);

		foreach ($projectReflectionClasses as $projectClass => $reflClass) {
			// Skip abstract classes, interfaces & traits, and non-service classes.
			if (
				$reflClass->isAbstract() ||
				$reflClass->isInterface() ||
				$reflClass->isTrait() ||
				!($reflClass->implementsInterface(ServiceInterface::class) || $reflClass->implementsInterface(ServiceCliInterface::class))
			) {
				continue;
			}

			// First-write-wins so the initial entry for a class survives later passes.
			foreach ($this->buildDependencyTree($projectClass, $filenameIndex, $classInterfaceIndex, $reflectionCache) as $class => $deps) {
				$dependencyTree[$class] ??= $deps;
			}
		}

		// Resolve transitive dependencies via a work list queue rather than mutating
		// the array while iterating it by reference.
		$queue = \array_keys($dependencyTree);
		while ($queue !== []) {
			$current = (string) \array_shift($queue);
			foreach (\array_keys($dependencyTree[$current] ?? []) as $depClass) {
				if (isset($dependencyTree[$depClass])) {
					continue;
				}

				foreach ($this->buildDependencyTree((string)$depClass, $filenameIndex, $classInterfaceIndex, $reflectionCache) as $newClass => $newDeps) {
					if (isset($dependencyTree[$newClass])) {
						continue;
					}

					$dependencyTree[$newClass] = $newDeps;
					$queue[] = $newClass;
				}
			}
		}

		// Convert dependency tree into PHP-DI's definition list.
		return [
			...$this->convertDependencyTreeIntoDefinitionList($dependencyTree),
			...$manuallyDefinedDependencies,
		];
	}

	// phpcs:disable Squiz.Commenting.FunctionCommentThrowTag.WrongNumber
	/**
	 * Builds the dependency tree for a single class ($relevantClass)
	 *
	 * @param string $relevantClass Class we're building dependency tree for.
	 * @param array<string, mixed> $filenameIndex Filename index. Maps filenames to class names.
	 * @param array<string, mixed> $classInterfaceIndex Class interface index. Map classes to interface they implement.
	 * @param array<string, ReflectionClass<object>> $reflectionCache Memoized ReflectionClass instances shared across the build.
	 *
	 * @throws InvalidAutowireDependency If a primitive dependency is found without a default value.
	 * @throws ReflectionException If reflection exception happens.
	 * @throws Exception General exception.
	 *
	 * @return array<string, mixed>
	 */
	private function buildDependencyTree(
		string $relevantClass,
		array $filenameIndex,
		array $classInterfaceIndex,
		array &$reflectionCache,
	): array {
		// Keeping PHPStan happy.
		if (!\class_exists($relevantClass, false)) {
			return [];
		}

		$reflClass = $reflectionCache[$relevantClass] ??= new ReflectionClass($relevantClass);
		$constructor = $reflClass->getConstructor();

		// No constructor or no parameters - register the class with no deps.
		if ($constructor === null || $constructor->getParameters() === []) {
			return [$relevantClass => []];
		}

		$dependencyTree = [];
		$hasDefaultedPrimitive = false;

		// Go through each constructor parameter.
		foreach ($constructor->getParameters() as $reflParam) {
			$type = $reflParam->getType();

			// Skip parameters without a single named type hint (preserves legacy behavior).
			if (!$type instanceof ReflectionNamedType) {
				continue;
			}

			$paramName = $reflParam->getName();
			$isBuiltin = $type->isBuiltin();

			if ($isBuiltin) {
				// Allowlisted primitives (Main's psr4Prefixes/namespace) — skip silently
				// without registering the owning class so Main isn't autowired.
				if (isset(self::IGNORED_PRIMITIVE_PARAMS[$paramName])) {
					continue;
				}

				// Primitive with a default value — PHP-DI will use the default at construction time.
				if ($reflParam->isDefaultValueAvailable() || $reflParam->isOptional()) {
					$hasDefaultedPrimitive = true;
					continue;
				}

				throw InvalidAutowireDependency::throwPrimitiveDependencyFound($relevantClass, $paramName);
			}

			$className = $type->getName();

			// Keeping PHPStan happy.
			if (!\class_exists($className, false) && !\interface_exists($className, false)) {
				continue;
			}

			$reflClassForParam = $reflectionCache[$className] ??= new ReflectionClass($className);

			// If the expected type is interface, try guessing based on var name.
			// Otherwise, just inject that class.
			if ($reflClassForParam->isInterface()) {
				$matchedClass = $this->tryToFindMatchingClass(
					$paramName,
					$className,
					$filenameIndex,
					$classInterfaceIndex
				);

				// If we're unable to find exactly 1 class for whatever reason, just skip it, the user
				// will have to define the dependencies manually.
				if ($matchedClass === '') {
					continue;
				}

				$dependencyTree[$relevantClass][$matchedClass] = [];
			} else {
				$dependencyTree[$relevantClass][$className] = [];
			}
		}

		// Class has resolvable defaults but no class-typed deps - still register so DI can construct it.
		if (!isset($dependencyTree[$relevantClass]) && $hasDefaultedPrimitive) {
			$dependencyTree[$relevantClass] = [];
		}

		return $dependencyTree;
	}
	// phpcs:enable

	/**
	 * Returns all classes in namespace.
	 *
	 * @param string $namespaceName Name of namespace.
	 * @param array<string, mixed> $psr4Prefixes Array of psr-4 compliant namespaces and their accompanying folders.
	 *
	 * @return string[]
	 */
	private function getClassesInNamespace(string $namespaceName, array $psr4Prefixes): array
	{
		$classes = [];
		$namespaceWithSlash = "{$namespaceName}\\";
		$pathToNamespace = $psr4Prefixes[$namespaceWithSlash][0] ?? '';

		if (!\is_dir($pathToNamespace)) {
			return [];
		}

		$it = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($pathToNamespace, RecursiveDirectoryIterator::SKIP_DOTS)
		);
		foreach ($it as $file) {
			if ($file->isDir()) {
				continue;
			}

			if (\preg_match('/^[A-Z][A-Za-z0-9]+\.php$/', $file->getFileName())) {
				$classes[] = $this->getNamespaceFromFilepath($file->getPathname(), $namespaceName, $pathToNamespace);
			}
		}

		return $classes;
	}

	/**
	 * Builds PSR namespace Vendor\from file's path.
	 *
	 * @param string $filepath Path to a file.
	 * @param string $rootNamespace Root namespace Vendor\we're getting classes from.
	 * @param string $rootNamespacePath Path to root namespace Vendor\.
	 *
	 * @return string
	 */
	private function getNamespaceFromFilepath(
		string $filepath,
		string $rootNamespace,
		string $rootNamespacePath
	): string {
		$pathNamespace = \str_replace(
			[$rootNamespacePath, \DIRECTORY_SEPARATOR, '.php'],
			['', '\\', ''],
			$filepath
		);

		return $rootNamespace . $pathNamespace;
	}


	/**
	 * Try to uniquely match the $filename.
	 *
	 * @param string $filename Filename based on variable name.
	 * @param string $interfaceName Interface we're trying to match.
	 * @param array<string, mixed> $filenameIndex Filename index. Maps filenames to class names.
	 * @param array<string, mixed> $classInterfaceIndex Class interface index. Map classes to interface they implement.
	 *
	 * @throws InvalidAutowireDependency If we didn't find exactly 1 class when trying to inject interface-based dependencies.
	 * @throws Exception If things we're looking for are missing inside filename or classInterface index (which shouldn't happen).
	 *
	 * @return string
	 */
	private function tryToFindMatchingClass(
		string $filename,
		string $interfaceName,
		array $filenameIndex,
		array $classInterfaceIndex
	): string {
		// If there's no matches in filename index by variable, we need to throw an exception to let the user
		// know they either need to provide the correct variable name OR manually define the dependencies for this class.
		$className = \ucfirst($filename);
		if (!isset($filenameIndex[$filename])) {
			throw InvalidAutowireDependency::throwUnableToFindClass($className, $interfaceName);
		}

		// Let's go through each file that's called $filename and check which interfaces that class
		// implements (if any).
		$matches = 0;
		$match = '';

		foreach ($filenameIndex[$filename] as $classInFilename) {
			if (!isset($classInterfaceIndex[$classInFilename])) {
				throw new Exception("Class {$classInFilename} not found in classInterfaceIndex, aborting.");
			}

			// If the current class implements the interface we're looking for, great!
			// We still need to go through all other classes to make sure we don't get more than 1 match.
			if (isset($classInterfaceIndex[$classInFilename][$interfaceName])) {
				$match = $classInFilename;
				$matches++;
			}
		}

		// If we don't have a unique match
		// (i.e. if 2 classes of the same name are implementing the interface we're looking for)
		// then we need to cancel the match because we don't know how to handle that.
		if ($matches === 0) {
			throw InvalidAutowireDependency::throwUnableToFindClass($className, $interfaceName);
		}

		if ($matches > 1) {
			throw InvalidAutowireDependency::throwMoreThanOneClassFound($className, $interfaceName);
		}

		return $match;
	}

	/**
	 * Builds the PSR-4 filename index. Maps filenames to class names.
	 *
	 * @param array<string, ReflectionClass<object>> $reflectionClasses Reflection classes of all relevant classes.
	 *
	 * @return array<string, mixed>
	 */
	private function buildFilenameIndex(array $reflectionClasses): array
	{
		$filenameIndex = [];
		foreach ($reflectionClasses as $relevantClass => $reflClass) {
			$filename = $this->getFilenameFromClass($relevantClass);

			$filenameIndex[$filename][] = $relevantClass;
		}

		return $filenameIndex;
	}

	/**
	 * Builds the PSR-4 class => [$interfaces] index. Map classes to interface they implement.
	 *
	 * @param array<string, ReflectionClass<object>> $reflectionClasses  Reflection classes of all relevant classes.
	 *
	 * @return array<string, mixed>
	 */
	private function buildClassInterfaceIndex(array $reflectionClasses): array
	{
		$classInterfaceIndex = [];
		foreach ($reflectionClasses as $projectClass => $reflectionClass) {
			$classInterfaceIndex[$projectClass] = \array_map(
				static fn() => true,
				$reflectionClass->getInterfaces()
			);
		}

		return $classInterfaceIndex;
	}

	/**
	 * Returns filename from fully-qualified class names
	 *
	 * Example: AutowiringTest/Something/Class => class
	 *
	 * @param string $className Fully qualified classname.
	 *
	 * @return string
	 */
	private function getFilenameFromClass(string $className): string
	{
		return \lcfirst(\trim(\substr($className, \strrpos($className, '\\') + 1)));
	}

	/**
	 * Takes the dependency tree array and convert's it into PHP-DI's definition list. Recursive.
	 *
	 * @param array<string, mixed> $dependencyTree Dependency tree.
	 *
	 * @return array<string, mixed>
	 */
	private function convertDependencyTreeIntoDefinitionList(array $dependencyTree): array
	{
		$classes = [];
		foreach ($dependencyTree as $className => $dependencies) {
			if (empty($dependencies)) {
				$classes[] = $className;
			} else {
				$classes[$className] = $this->convertDependencyTreeIntoDefinitionList($dependencies);
			}
		}

		return $classes;
	}

	/**
	 * Validates all classes.
	 *
	 * Validates that all classes/interfaces/traits/etc. provided here are valid (we can build a ReflectionClass
	 * on them) and return them. Otherwise, throw an exception.
	 *
	 * @param array<string, mixed> $classNames FQCNs found in $this->namespace.
	 * @param bool  $skipInvalid Skip invalid namespaces rather than throwing an exception. Used for tests.
	 *
	 * @return array<string, ReflectionClass<object>>
	 *
	 * @throws NonPsr4CompliantClass When a found class/file doesn't match PSR-4 standards (and $skipInvalid is false).
	 */
	private function validateAndBuildClasses(array $classNames, bool $skipInvalid): array
	{
		$reflectionClasses = [];
		foreach ($classNames as $className) {
			try {
				$reflClass = new ReflectionClass($className);
				$reflectionClasses[(string)$className] = $reflClass;
			} catch (Exception $e) {
				if ($skipInvalid) {
					continue;
				} else {
					throw NonPsr4CompliantClass::throwInvalidNamespace($className);
				}
			}
		}

		return $reflectionClasses;
	}

	/**
	 * Filters out manually defined dependencies so we don't autowire them.
	 *
	 * @param array<string, mixed> $serviceClasses All FQCNs inside the namespace.
	 * @param array<string, mixed> $manuallyDefinedDependencies Manually defined dependency tree.
	 *
	 * @return array<string, mixed>
	 */
	private function filterManuallyDefinedDependencies(array $serviceClasses, array $manuallyDefinedDependencies): array
	{
		return \array_filter($serviceClasses, function ($classNamespace) use ($manuallyDefinedDependencies) {
			return !isset($manuallyDefinedDependencies[$classNamespace]);
		});
	}
}
