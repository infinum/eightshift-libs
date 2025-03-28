<?php

/**
 * Helpers for cache.
 *
 * @package EightshiftLibs\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Helpers;

use EightshiftLibs\Cache\AbstractManifestCache;

/**
 * Class CacheTrait Helper
 */
trait CacheTrait
{
	/**
	 * Cache.
	 *
	 * @var array<mixed>
	 */
	private static $cache = [];

	/**
	 * Cache builder.
	 *
	 * @var array<mixed>
	 */
	private static $cacheBuilder = [];

	/**
	 * Cache name.
	 *
	 * @var string
	 */
	private static $cacheName = '';

	/**
	 * Cache version.
	 *
	 * @var string
	 */
	private static $version = '';

	// -----------------------------------------------------
	// CACHE
	// -----------------------------------------------------

	/**
	 * Set cache details.
	 *
	 * @param array<mixed> $cacheBuilder Cache builder.
	 * @param string $cacheName Cache name.
	 * @param string $version Cache version.
	 *
	 * @return void
	 */
	public static function setCacheDetails(
		array $cacheBuilder,
		string $cacheName,
		string $version
	): void {
		self::$cacheBuilder = $cacheBuilder;
		self::$cacheName = $cacheName;
		self::$version = $version;
	}

	/**
	 * Set internal cache.
	 *
	 * @return void
	 */
	public static function setCache(): void
	{
		if (self::$cache) {
			return;
		}

		$output = [];

		foreach (\array_keys(self::$cacheBuilder) as $type) {
			$data = \get_transient(self::getCacheTransientName($type));

			if (!$data) {
				continue;
			}

			if (!Helpers::isJson($data)) {
				continue;
			}

			$data = \json_decode($data, true);

			if (!$data) {
				continue;
			}

			$output[$type] = $data;
		}

		self::$cache = $output;
	}

	/**
	 * Get cache.
	 *
	 * @return array<mixed>
	 */
	public static function getCache(): array
	{
		return self::$cache;
	}

	/**
	 * Get cache name.
	 *
	 * @return string
	 */
	public static function getCacheName(): string
	{
		return self::$cacheName;
	}

	/**
	 * Get cache transient name.
	 *
	 * @param string $type Type of the cache.
	 *
	 * @return string
	 */
	public static function getCacheTransientName(string $type): string
	{
		return AbstractManifestCache::TRANSIENT_NAME . "_{$type}";
	}

	/**
	 * Check if cache version is valid.
	 *
	 * @return bool
	 */
	public static function isCacheVersionValid(): bool
	{
		return self::getCacheVersion() === self::$version;
	}

	/**
	 * Set version cache.
	 *
	 * @return void
	 */
	public static function setCacheVersion(): void
	{
		$name = self::getCacheTransientName(AbstractManifestCache::VERSION_KEY);

		$cache = \get_transient($name);

		if (!$cache) {
			\set_transient($name, self::$version);
		}
	}

	/**
	 * Get cache version.
	 *
	 * @return string
	 */
	public static function getCacheVersion(): string
	{
		$cache = \get_transient(self::getCacheTransientName(AbstractManifestCache::VERSION_KEY)) ?: ''; // phpcs:ignore WordPress.PHP.DisallowShortTernary.Found

		if (!$cache) {
			self::setCacheVersion();
		}

		return $cache;
	}

	/**
	 * Unset cache version.
	 *
	 * @return void
	 */
	public static function deleteCacheVersion(): void
	{
		\delete_transient(self::getCacheTransientName(AbstractManifestCache::VERSION_KEY));
	}

	/**
	 * Unset all manifest cache.
	 *
	 * @return void
	 */
	public static function deleteAllCache(): void
	{
		$data = \array_keys(self::$cacheBuilder);

		foreach ($data as $cache) {
			self::deleteCache($cache);
		}

		self::deleteCacheVersion();
	}

	/**
	 * Unset cache item by type.
	 *
	 * @param string $cacheType Type of the cache.
	 *
	 * @return void
	 */
	public static function deleteCache(string $cacheType): void
	{
		\delete_transient(self::getCacheTransientName($cacheType));
	}

	/**
	 * Check if we should cache the service classes.
	 *
	 * @return bool
	 */
	public static function shouldCache(): bool
	{
		return !(
			(\defined('WP_ENVIRONMENT_TYPE') &&
			(\WP_ENVIRONMENT_TYPE === 'development' || \WP_ENVIRONMENT_TYPE === 'local')) ||
			\defined('WP_CLI')
		);
	}
}
