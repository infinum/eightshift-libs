<?php

/**
 * File containing the main intro class for your project.
 *
 * @package EightshiftLibs\Main
 */

declare(strict_types=1);

namespace EightshiftLibs\Main;

use DI\Container;
use DI\ContainerBuilder;
use DI\Definition\Helper\AutowireDefinitionHelper;
use DI\Definition\Reference;
use EightshiftLibs\ClassAttributes\ShouldLoadInCliContext;
use EightshiftLibs\Helpers\Helpers;
use EightshiftLibs\Services\ServiceInterface;
use EightshiftLibs\Services\ServiceCliInterface;
// phpcs:ignore SlevomatCodingStandard.Namespaces.UnusedUses.UnusedUse
use Exception;
use ReflectionClass;

/**
 * The main start class.
 *
 * This is used to define instantiate all classes used in the lib.
 */
abstract class AbstractMain extends Autowiring implements ServiceInterface
{
	/**
	 * Array of instantiated services.
	 *
	 * @var Object[]
	 */
	protected array $services = [];

	/**
	 * DI container instance.
	 *
	 * @var Container
	 */
	protected Container $container;

	/**
	 * Constructs object and inserts prefixes from composer.
	 *
	 * @param array<string, mixed> $psr4Prefixes Composer's ClassLoader psr4Prefixes. $ClassLoader->getPsr4Prefixes().
	 * @param string $projectNamespace Projects namespace.
	 */
	public function __construct(array $psr4Prefixes, string $projectNamespace)
	{
		$this->psr4Prefixes = $psr4Prefixes;
		$this->namespace = $projectNamespace;
	}

	/**
	 * Register the individual services with optional dependency injection.
	 *
	 * @throws Exception Exception thrown by DI container.
	 *
	 * @return void
	 */
	public function registerServices()
	{
		// Bail early so we don't instantiate services twice.
		if (!empty($this->services)) {
			return;
		}

		$this->services = $this->getServiceClassesWithDi();

		\array_walk(
			$this->services,
			function ($class) {
				// Load services classes but not in the WP-CLI env, unless they have the ShouldLoadInCliContext attr.
				if (!\defined('WP_CLI') && $class instanceof ServiceInterface) {
					$class->register();
				}

				if (\defined('WP_CLI')) {
					if ($class instanceof ServiceCliInterface) {
						// Classes implementing ServiceCliInterface should be loaded only in CLI contexts.
						$class->register();
						return;
					}

					// Allow loading service classes in CLI contexts if it
					// or a parent class has ShouldLoadInCliContext attribute.
					$reflection = new ReflectionClass($class);
					while ($reflection) {
						if (\count($reflection->getAttributes(ShouldLoadInCliContext::class))) {
							$class->register();
							return;
						}

						$reflection = $reflection->getParentClass();
					}
				}
			}
		);
	}

	/**
	 * Returns the DI container
	 *
	 * Allows it to be used in different context (for example in tests outside of WP environment).
	 *
	 * @return Container
	 * @throws Exception Exception thrown by the DI container.
	 */
	public function buildDiContainer(): Container
	{
		$this->container = $this->getDiContainer($this->getServiceClassesPreparedArray());

		return $this->container;
	}

	/**
	 * Merges the autowired definition list with custom user-defined definition list.
	 *
	 * You can override autowired definition lists in $this->getServiceClasses().
	 *
	 * @throws Exception Exception thrown in case class is missing.
	 *
	 * @return array<string, mixed>
	 */
	private function getServiceClassesWithAutowire(): array
	{
		return $this->buildServiceClasses($this->getServiceClasses());
	}

	/**
	 * Return array of services with Dependency Injection parameters.
	 *
	 * @return Object[]
	 *
	 * @throws Exception Exception thrown by the DI container.
	 */
	protected function getServiceClassesWithDi(): array
	{
		$services = $this->getServiceClassesPreparedArray();

		if (!$services) {
			return [];
		}

		$services = $this->createServiceClassesCacheFile($services);

		$container = $this->getDiContainer($services);

		return \array_map(
			function ($class) use ($container) {
				return $container->get($class);
			},
			\array_keys($services)
		);
	}

	/**
	 * Get services classes array and prepare it for dependency injection.
	 * Key should be a class name, and value should be an empty array or the dependencies of the class.
	 *
	 * @throws Exception Exception thrown in case class is missing.
	 *
	 * @return array<string, mixed>
	 */
	private function getServiceClassesPreparedArray(): array
	{
		$output = [];

		foreach ($this->getServiceClassesWithAutowire() as $class => $dependencies) {
			if (\is_array($dependencies)) {
				$output[$class] = $dependencies;
				continue;
			}

			$output[$dependencies] = [];
		}

		return $output;
	}

	/**
	 * Implement PHP-DI.
	 *
	 * Build and return a DI container.
	 * Wire all the dependencies automatically, based on the provided array of
	 * class => dependencies from the get_di_items().
	 *
	 * @param array<string, mixed> $services Array of service.
	 *
	 * @throws Exception Exception thrown by the DI container.
	 *
	 * @return Container
	 */
	private function getDiContainer(array $services): Container
	{
		$definitions = [];

		foreach ($services as $serviceKey => $serviceValues) {
			if (\gettype($serviceValues) !== 'array') {
				continue;
			}

			$autowire = new AutowireDefinitionHelper();

			$definitions[$serviceKey] = $autowire->constructor(...$this->getDiDependencies($serviceValues));
		}

		$builder = new ContainerBuilder();

		if (Helpers::shouldCache()) {
			$fileName = \explode('\\', $this->namespace);
			$builder->enableCompilation(Helpers::getEightshiftOutputPath(), "{$fileName[0]}CompiledContainer");
		}

		return $builder->addDefinitions($definitions)->build();
	}

	/**
	 * Return prepared Dependency Injection objects.
	 * If you pass a class use PHP-DI to prepare if not just output it.
	 *
	 * @param array<string, mixed> $dependencies Array of classes/parameters to push in constructor.
	 *
	 * @return array<string, mixed>
	 */
	private function getDiDependencies(array $dependencies): array
	{
		return \array_map(
			function ($dependency) {
				if (\class_exists($dependency)) {
					return new Reference($dependency);
				}
				return $dependency;
			},
			$dependencies
		);
	}

	/**
	 * Get the list of services to register.
	 *
	 * A list of classes which contain hooks.
	 *
	 * @return array<class-string, string|string[]> Array of fully qualified service class names.
	 */
	protected function getServiceClasses(): array
	{
		return [];
	}

	/**
	 * Create the service classes cache file and return the services array.
	 *
	 * @param array<string, mixed> $services Array of services.
	 *
	 * @return array<string, mixed>
	 */
	private function createServiceClassesCacheFile(array $services): array
	{
		if (Helpers::shouldCache()) {
			$file = \explode('\\', $this->namespace);

			$cacheFile = Helpers::getEightshiftOutputPath("{$file[0]}ServiceClasses.json");

			if (\file_exists($cacheFile)) {
				return \json_decode(\file_get_contents($cacheFile), true); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			}

			if (\file_put_contents($cacheFile, \wp_json_encode($services))) { // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
				\chmod($cacheFile, 0644); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_chmod
			}

			return $services;
		}

		return $services;
	}
}
