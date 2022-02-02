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
use EightshiftLibs\Services\ServiceInterface;

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
	private $services = [];

	/**
	 * DI container instance.
	 *
	 * @var Container
	 */
	private $container;

	/**
	 * Constructs object and inserts prefixes from composer.
	 *
	 * @param array<string, mixed> $psr4Prefixes Composer's ClassLoader psr4Prefixes. $ClassLoader->getPsr4Prefixes().
	 * @param string $namespace Projects namespace.
	 */
	public function __construct(array $psr4Prefixes, string $namespace)
	{
		$this->psr4Prefixes = $psr4Prefixes;
		$this->namespace = $namespace;
	}

	/**
	 * Register the individual services with optional dependency injection.
	 *
	 * @throws \Exception Exception thrown by DI container.
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

		array_walk(
			$this->services,
			function ($class) {
				if (!$class instanceof ServiceInterface) {
					return;
				}

				$class->register();
			}
		);
	}

	/**
	 * Returns the DI container
	 *
	 * Allows it to be used in different context (for example in tests outside of WP environment).
	 *
	 * @return Container
	 * @throws \Exception Exception thrown by the DI container.
	 */
	public function buildDiContainer(): Container
	{
		if (empty($this->container)) {
			$this->container = $this->getDiContainer($this->getServiceClassesPreparedArray());
		}
		return $this->container;
	}

	/**
	 * Merges the autowired definition list with custom user-defined definition list.
	 *
	 * You can override autowired definition lists in $this->getServiceClasses().
	 *
	 * @throws \ReflectionException Exception thrown in case class is missing.
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
	 * @throws \Exception Exception thrown by the DI container.
	 */
	private function getServiceClassesWithDi(): array
	{
		$services = $this->getServiceClassesPreparedArray();

		$container = $this->getDiContainer($services);

		return array_map(
			function ($class) use ($container) {
				return $container->get($class);
			},
			array_keys($services)
		);
	}

	/**
	 * Get services classes array and prepare it for dependency injection.
	 * Key should be a class name, and value should be an empty array or the dependencies of the class.
	 *
	 * @throws \ReflectionException Exception thrown in case class is missing.
	 *
	 * @return array<string, mixed>
	 */
	private function getServiceClassesPreparedArray(): array
	{
		$output = [];

		foreach ($this->getServiceClassesWithAutowire() as $class => $dependencies) {
			if (is_array($dependencies)) {
				$output[$class] = $dependencies;
				continue;
			}

			$output[$dependencies] = [];
		}

		return $output; // @phpstan-ignore-line
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
	 * @throws \Exception Exception thrown by the DI container.
	 *
	 * @return Container
	 */
	private function getDiContainer(array $services): Container
	{
		$definitions = [];

		foreach ($services as $serviceKey => $serviceValues) {
			if (gettype($serviceValues) !== 'array') {
				continue;
			}

			$autowire = new AutowireDefinitionHelper();

			$definitions[$serviceKey] = $autowire->constructor(...$this->getDiDependencies($serviceValues));
		}

		$builder = new ContainerBuilder();

		if (defined('WP_ENVIRONMENT_TYPE') && (WP_ENVIRONMENT_TYPE === 'production' || WP_ENVIRONMENT_TYPE === 'staging')) {
			$file = explode('\\', $this->namespace);

			$builder->enableCompilation(__DIR__ . '/Cache', "{$file[0]}CompiledContainer");
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
		return array_map(
			function ($dependency) {
				if (class_exists($dependency)) {
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
}
