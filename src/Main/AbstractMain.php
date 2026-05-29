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
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

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
	 * @var object[]
	 */
	protected array $services = [];

	/**
	 * DI container instance.
	 *
	 * @var Container
	 */
	protected Container $container;

	/**
	 * Register the individual services with optional dependency injection.
	 *
	 * @throws Exception Exception thrown by DI container.
	 */
	public function registerServices(): void
	{
		if ($this->services !== []) {
			return;
		}

		$this->services = $this->getServiceClassesWithDi();

		$isCli = \defined('WP_CLI');
		$cliLoadCache = [];

		foreach ($this->services as $class) {
			if (!$isCli) {
				if ($class instanceof ServiceInterface) {
					$class->register();
				}
				continue;
			}

			if ($class instanceof ServiceCliInterface) {
				$class->register();
				continue;
			}

			$className = $class::class;
			$cliLoadCache[$className] ??= $this->classWantsCliLoad($class);
			if ($cliLoadCache[$className]) {
				$class->register();
			}
		}
	}

	/**
	 * Returns the DI container
	 *
	 * Allows it to be used in different context (for example in tests outside of WP environment).
	 *
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
	 * @return object[]
	 *
	 * @throws Exception Exception thrown by the DI container.
	 */
	protected function getServiceClassesWithDi(): array
	{
		$services = $this->getServiceClassesPreparedArray();

		if ($services === []) {
			return [];
		}

		$services = $this->maybeCacheProductionServices($services);
		$container = $this->getDiContainer($services);

		$instances = [];
		foreach (\array_keys($services) as $class) {
			$instances[] = $container->get($class);
		}

		return $instances;
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
		$devCacheEnabled = $this->isDevServiceCacheEnabled();
		$devCachePath = $devCacheEnabled ? $this->getCachePath('DevServiceClasses.json') : '';

		if ($devCacheEnabled) {
			$cached = $this->loadServicesCache($devCachePath, true);
			if ($cached !== null) {
				return $cached;
			}
		}

		$output = [];
		foreach ($this->getServiceClassesWithAutowire() as $class => $dependencies) {
			if (\is_array($dependencies)) {
				$output[$class] = $dependencies;
				continue;
			}

			$output[$dependencies] = [];
		}

		if ($devCacheEnabled) {
			$this->storeServicesCache($devCachePath, $output, true);
		}

		return $output;
	}

	/**
	 * Read or write the production service-classes cache.
	 *
	 * Returns the cached services when available, otherwise persists the input array and returns it.
	 * Returns the input untouched when production caching is disabled.
	 *
	 * @param array<string, mixed> $services Services to cache if no cache exists yet.
	 *
	 * @return array<string, mixed>
	 */
	private function maybeCacheProductionServices(array $services): array
	{
		if (!Helpers::shouldCache()) {
			return $services;
		}

		$path = $this->getCachePath('ServiceClasses.json');

		$cached = $this->loadServicesCache($path, false);
		if ($cached !== null) {
			return $cached;
		}

		$this->storeServicesCache($path, $services, false);

		return $services;
	}

	/**
	 * Load a cached services array from disk.
	 *
	 * @param string $path Absolute cache file path.
	 * @param bool $checkMtime When true, the cache is invalidated if the namespace mtime changed.
	 *
	 * @return array<string, mixed>|null Cached services, or null if missing/stale/malformed.
	 */
	private function loadServicesCache(string $path, bool $checkMtime): ?array
	{
		if (!\is_file($path)) {
			return null;
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$content = \file_get_contents($path);
		if ($content === false || $content === '') {
			return null;
		}

		$payload = \json_decode($content, true);
		if (
			!\is_array($payload) ||
			!isset($payload['services']) ||
			!\is_array($payload['services'])
		) {
			return null;
		}

		if ($checkMtime && (int) ($payload['mtime'] ?? -1) !== $this->getNamespaceMaxMtime()) {
			return null;
		}

		return $payload['services'];
	}

	/**
	 * Persist a services array to the cache file atomically.
	 *
	 * @param string $path Absolute cache file path.
	 * @param array<string, mixed> $services Services to persist.
	 * @param bool $includeMtime When true, embed the namespace mtime for later invalidation.
	 */
	private function storeServicesCache(string $path, array $services, bool $includeMtime): void
	{
		$directory = \dirname($path);

		if (!\is_dir($directory) && !\mkdir($directory, 0755, true) && !\is_dir($directory)) {
			return;
		}

		$payload = ['services' => $services];
		if ($includeMtime) {
			$payload['mtime'] = $this->getNamespaceMaxMtime();
		}

		$encoded = \wp_json_encode($payload);
		if (!\is_string($encoded)) {
			return;
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
		if (\file_put_contents($path, $encoded, \LOCK_EX) !== false) {
			\chmod($path, 0644); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_chmod
		}
	}

	/**
	 * Whether the development autowiring cache should be consulted.
	 *
	 * Disabled in production (compiled container handles it) and under WP-CLI
	 * to ensure scaffolding sees freshly added classes.
	 */
	private function isDevServiceCacheEnabled(): bool
	{
		if (Helpers::shouldCache()) {
			return false;
		}
		return !(\defined('WP_CLI') && \WP_CLI);
	}

	/**
	 * Absolute path to a cache file within the Eightshift output directory.
	 *
	 * Namespaced by the first segment of the project's PHP namespace so multiple
	 * AbstractMain subclasses don't collide.
	 *
	 * @param string $filename Filename including extension.
	 */
	private function getCachePath(string $filename): string
	{
		return Helpers::getEightshiftOutputPath("{$this->getNamespaceRoot()}{$filename}");
	}

	/**
	 * First segment of the configured namespace, used as a cache key prefix.
	 */
	private function getNamespaceRoot(): string
	{
		static $cache = [];
		return $cache[$this->namespace] ??= \explode('\\', $this->namespace)[0];
	}

	/**
	 * Maximum mtime of any PHP file under the namespace's psr-4 root.
	 *
	 * Memoized per namespace so independent AbstractMain subclasses do not poison each other's cache.
	 */
	private function getNamespaceMaxMtime(): int
	{
		static $cache = [];
		if (isset($cache[$this->namespace])) {
			return $cache[$this->namespace];
		}

		$namespaceWithSlash = "{$this->namespace}\\";
		$pathToNamespace = $this->psr4Prefixes[$namespaceWithSlash][0] ?? '';

		if (!\is_string($pathToNamespace) || !\is_dir($pathToNamespace)) {
			return $cache[$this->namespace] = 0;
		}

		$max = 0;
		$iterator = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($pathToNamespace, RecursiveDirectoryIterator::SKIP_DOTS)
		);

		foreach ($iterator as $entry) {
			if (!$entry->isFile()) {
				continue;
			}
			if ($entry->getExtension() !== 'php') {
				continue;
			}
			$mtime = $entry->getMTime();
			if ($mtime > $max) {
				$max = $mtime;
			}
		}

		return $cache[$this->namespace] = $max;
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
	 */
	private function getDiContainer(array $services): Container
	{
		$definitions = [];

		foreach ($services as $serviceKey => $serviceValues) {
			if (!\is_array($serviceValues)) {
				continue;
			}

			$autowire = new AutowireDefinitionHelper();
			$definitions[$serviceKey] = $autowire->constructor(...$this->getDiDependencies($serviceValues));
		}

		$builder = new ContainerBuilder();

		if (Helpers::shouldCache()) {
			$builder->enableCompilation(
				Helpers::getEightshiftOutputPath(),
				"{$this->getNamespaceRoot()}CompiledContainer"
			);
		}

		return $builder->addDefinitions($definitions)->build();
	}

	/**
	 * Return prepared Dependency Injection objects.
	 *
	 * If a dependency value is a known class name it becomes a `Reference`, otherwise it is
	 * passed through unchanged. `class_exists` lookups are memoized to avoid repeated autoload hits.
	 *
	 * @param array<string, mixed> $dependencies Array of classes/parameters to push in constructor.
	 *
	 * @return array<int, mixed>
	 */
	private function getDiDependencies(array $dependencies): array
	{
		static $classExistsCache = [];

		$resolved = [];
		foreach ($dependencies as $dependency) {
			if (\is_string($dependency)) {
				$exists = $classExistsCache[$dependency] ??= \class_exists($dependency);
				if ($exists) {
					$resolved[] = new Reference($dependency);
					continue;
				}
			}

			$resolved[] = $dependency;
		}

		return $resolved;
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
	 * Determine whether a service class (or any ancestor) is marked with
	 * the ShouldLoadInCliContext attribute.
	 *
	 * @param object $class Service instance.
	 */
	private function classWantsCliLoad(object $class): bool
	{
		$reflection = new ReflectionClass($class);

		while ($reflection !== false) {
			if ($reflection->getAttributes(ShouldLoadInCliContext::class) !== []) {
				return true;
			}
			$reflection = $reflection->getParentClass();
		}

		return false;
	}
}
