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

/**
 * The file that defines the autowiring process
 */
class Autowiring
{

	/**
	 * Array of psr-4 prefixes. Should be provided by Composer's ClassLoader. $ClassLoader->getPsr4Prefixes().
	 *
	 * @var array
	 */
	protected $psr4Prefixes;

	/**
	 * Project namespace
	 *
	 * @var string
	 */
	protected $namespace;

	/**
	 * Autowiring.
	 *
	 * @param array $manuallyDefinedDependencies Manually defined dependencies from Main.
	 * @param bool  $skipInvalid Skip invalid namespaces rather than throwing an exception. Used for tests.
	 *
	 * @throws \ReflectionException Exception thrown in case class is missing.
	 *
	 * @return array<array> Array of fully qualified class names.
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

		// Prepare the filename index.
		$filenameIndex = $this->buildFilenameIndex($projectReflectionClasses);
		$classInterfaceIndex = $this->buildClassInterfaceIndex($projectReflectionClasses);

		foreach ($projectReflectionClasses as $projectClass => $reflClass) {
			// Skip abstract classes, interfaces & traits, and non service classes.
			if (
				$reflClass->isAbstract() ||
				$reflClass->isInterface() ||
				$reflClass->isTrait() ||
				!$reflClass->implementsInterface(ServiceInterface::class)
			) {
				continue;
			}

			// Build the dependency tree.
			$dependencyTree = array_merge(
				$this->buildDependencyTree($projectClass, $filenameIndex, $classInterfaceIndex),
				$dependencyTree
			);
		}

		// Build dependency tree for dependencies. Things that need to be injected but were skipped because
		// they were initially irrelevant.
		foreach ($dependencyTree as $dependencies) {
			foreach ($dependencies as $depClass => $subDeps) {
				// No need to build dependencies for this again if we already have them.
				if (isset($dependencyTree[$depClass])) {
					continue;
				}

				$dependencyTree = array_merge(
					$this->buildDependencyTree($depClass, $filenameIndex, $classInterfaceIndex),
					$dependencyTree
				);
			}
		}

		// Convert dependency tree into PHP-DI's definition list.
		return array_merge($this->convertDependencyTreeIntoDefinitionList($dependencyTree), $manuallyDefinedDependencies);
	}

	/**
	 * Builds the dependency tree for a single class ($relevantClass)
	 *
	 * @param string $relevantClass Class we're building dependency tree for.
	 * @param array  $filenameIndex Filename index. Maps filenames to class names.
	 * @param array  $classInterfaceIndex Class interface index. Maps classes to interfaces they implement.
	 *
	 * @throws InvalidAutowireDependency If a primitive dependency is found.
	 *
	 * @return array
	 */
	private function buildDependencyTree(string $relevantClass, array $filenameIndex, array $classInterfaceIndex): array
	{
		// Keeping PHPStan happy.
		if (!class_exists($relevantClass, false)) {
			return [];
		}

		$dependencyTree = [];
		$reflClass = new \ReflectionClass($relevantClass);
		// If this class has dependencies, we need to figure those out. Otherwise
		// we just add it to the dependency tree as a class without dependencies.
		if (!empty($reflClass->getConstructor()) && !empty($reflClass->getConstructor()->getParameters())) {
			// Go through each constructor parameter.
			foreach ($reflClass->getConstructor()->getParameters() as $reflParam) {
				$type = $reflParam->getType();

				// Skip parameters without type hints.
				if ($type instanceof \ReflectionNamedType) {
					$className = $type->getName();
					$isBuiltin = $type->isBuiltin();
				} else {
					continue;
				}

				// We're unable to autowire primitive dependency and there doesn't seem to yet be a way
				// to check if this parameter has a default value or not (so we need to throw an exception regardless).
				// See: https://www.php.net/manual/en/class.reflectionnamedtype.php.
				if ($isBuiltin) {
					throw InvalidAutowireDependency::throwPrimitiveDependencyFound($relevantClass);
				}

				// Keeping PHPStan happy.
				if (class_exists($className, false) || interface_exists($className, false)) {
					$reflClassForParam = new \ReflectionClass($className);

					// If the expected type is interface, try guessing based on var name.
					// Otherwise just inject that class.
					if ($reflClassForParam->isInterface()) {
						$matchedClass = $this->tryToFindMatchingClass(
							$reflParam->getName(),
							$className,
							$filenameIndex,
							$classInterfaceIndex
						);

						// If we're unable to find exactly 1 class for whatever reason, just skip it, the user
						// will have to define the dependencies manually.
						if (empty($matchedClass)) {
							continue;
						}
						$dependencyTree[$relevantClass][$matchedClass] = [];
					} else {
						$dependencyTree[$relevantClass][$className] = [];
					}
				}
			}
		} else {
			$dependencyTree[$relevantClass] = [];
		}
		return $dependencyTree;
	}

	/**
	 * Returns all classes in namespace.
	 *
	 * @param string $namespace Name of namespace.
	 * @param array  $psr4Prefixes Array of psr-4 compliant namespaces and their accompanying folders.
	 *
	 * @return array
	 */
	private function getClassesInNamespace(string $namespace, array $psr4Prefixes): array
	{
		$classes = [];
		$namespaceWithSlash = "{$namespace}\\";
		$pathToNamespace = $psr4Prefixes[$namespaceWithSlash][0] ?? '';

		if (!is_dir($pathToNamespace)) {
			return [];
		}

		$it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($pathToNamespace));
		foreach ($it as $file) {
			if ($file->isDir()) {
				continue;
			}

			if (preg_match('/^[A-Z]{1}[A-Za-z0-9]+\.php/', $file->getFileName())) {
				$classes[] = $this->getNamespaceFromFilepath($file->getPathname(), $namespace, $pathToNamespace);
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
		$pathNamespace = str_replace(
			[$rootNamespacePath, DIRECTORY_SEPARATOR, '.php'],
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
	 * @param array  $filenameIndex Filename index. Maps filenames to class names.
	 * @param array  $classInterfaceIndex Class interface index. Maps classes to interfaces they implement.
	 *
	 * @throws InvalidAutowireDependency If we didn't find exactly 1 class when trying to inject interface-based dependencies.
	 * @throws \Exception If things we're looking for are missing inside filename or classInterface index (which shouldn't happen).
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
		$className = ucfirst($filename);
		if (!isset($filenameIndex[$filename])) {
			throw InvalidAutowireDependency::throwUnableToFindClass($className, $interfaceName);
		}

		// Lets go through each file that's called $filename and check which interfaces that class
		// implements (if any).
		$matches = 0;
		$match = '';

		foreach ($filenameIndex[$filename] as $classInFilename) {
			if (!isset($classInterfaceIndex[$classInFilename])) {
				throw new \Exception("Class {$classInFilename} not found in classInterfaceIndex, aborting.");
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
	 * @param array $reflectionClasses Reflection classes of all relevant classes.
	 *
	 * @return array
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
	 * Builds the PSR-4 class => [$interfaces] index. Maps classes to interfaces they implement.
	 *
	 * @param array $reflectionClasses  Reflection classes of all relevant classes.
	 *
	 * @throws \ReflectionException Exception thrown in case class is missing.
	 *
	 * @return array
	 */
	private function buildClassInterfaceIndex(array $reflectionClasses): array
	{
		$classInterfaceIndex = [];
		foreach ($reflectionClasses as $projectClass => $reflectionClass) {
			$interfaces = array_map(
				function () {
					return true;
				},
				$reflectionClass->getInterfaces()
			);

			$classInterfaceIndex[$projectClass] = $interfaces;
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
		return lcfirst(trim(substr($className, strrpos($className, '\\') + 1)));
	}

	/**
	 * Takes the dependency tree array and convert's it into PHP-DI's definition list. Recursive.
	 *
	 * @param array $dependencyTree Dependency tree.
	 *
	 * @return array
	 */
	private function convertDependencyTreeIntoDefinitionList(array $dependencyTree)
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
	 * on them) and return them. Otherwise throw an exception.
	 *
	 * @param array $classNames FQCNs found in $this->namespace.
	 * @param bool  $skipInvalid Skip invalid namespaces rather than throwing an exception. Used for tests.
	 * @return array
	 *
	 * @throws NonPsr4CompliantClass When a found class/file doesn't match PSR-4 standards (and $skipInvalid is false).
	 */
	private function validateAndBuildClasses(array $classNames, bool $skipInvalid): array
	{
		$reflectionClasses = [];
		foreach ($classNames as $className) {
			try {
				$reflClass = new \ReflectionClass($className);
				$reflectionClasses[$className] = $reflClass;
			} catch (\Exception $e) {
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
	 * @param array $serviceClasses All FQCNs inside the namespace.
	 * @param array $manuallyDefinedDependencies Manually defined dependency tree.
	 * @return array
	 */
	private function filterManuallyDefinedDependencies(array $serviceClasses, array $manuallyDefinedDependencies): array
	{
		return array_filter($serviceClasses, function ($classNamespace) use ($manuallyDefinedDependencies) {
			return !isset($manuallyDefinedDependencies[$classNamespace]);
		});
	}
}
