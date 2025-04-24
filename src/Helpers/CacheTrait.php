<?php

/**
 * Helpers for cache.
 *
 * @package EightshiftLibs\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Helpers;

use EightshiftLibs\Cache\AbstractManifestCache;
use EightshiftLibs\Exception\InvalidManifest;

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

	/**
	 * Cache duration.
	 *
	 * @var int
	 */
	private static $duration = 0;

	/**
	 * Namespace for blocks.
	 *
	 * @var string
	 */
	private static $blocksNamespace = '';

	// -----------------------------------------------------
	// CACHE
	// -----------------------------------------------------

	/**
	 * Set cache details.
	 *
	 * @param array<mixed> $cacheBuilder Cache builder.
	 * @param string $cacheName Cache name.
	 * @param string $version Cache version.
	 * @param int $duration Cache duration.
	 *
	 * @return void
	 */
	public static function setCacheDetails(
		array $cacheBuilder,
		string $cacheName,
		string $version,
		int $duration,
	): void {
		self::$cacheBuilder = $cacheBuilder;
		self::$cacheName = $cacheName;
		self::$version = $version;
		self::$duration = $duration;

		if (!Helpers::isCacheVersionValid() || !Helpers::shouldCache()) {
			Helpers::deleteAllCache();
		}

		Helpers::setCacheVersion();
		Helpers::setAllCache();
	}

	/**
	 * Set internal cache.
	 *
	 * @return void
	 */
	public static function setAllCache(): void
	{
		if (self::$cache) {
			return;
		}

		if (!Helpers::shouldCache()) {
			self::$cache = self::getAllManifests();
			return;
		}

		$data = \get_transient(Helpers::getCacheTransientName());

		if (!$data) {
			$data = self::getAllManifests();

			\set_transient(
				Helpers::getCacheTransientName(),
				\wp_json_encode($data),
				self::$duration
			);

			self::$cache = $data;

			return;
		}

		$data = \json_decode($data, true);
		if (!$data) {
			self::$cache = [];
			return;
		}

		self::$cache = $data;

		return;
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
	public static function getCacheTransientName(string $type = ''): string
	{
		$name = AbstractManifestCache::TRANSIENT_PREFIX_NAME . '_' . self::$cacheName;

		if (!$type) {
			return $name;
		}

		return "{$name}_{$type}";
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
	 * Unset all manifest cache.
	 *
	 * @return void
	 */
	public static function deleteAllCache(): void
	{
		\delete_transient(self::getCacheTransientName());
		\delete_transient(self::getCacheTransientName(AbstractManifestCache::VERSION_KEY));
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

	/**
	 * Get all manifests from the paths.
	 *
	 * @return array<string, array<mixed>> Array of manifests.
	 */
	private static function getAllManifests(): array
	{
		$output = [];

		foreach (self::$cacheBuilder as $type => $item) {
			foreach ($item as $parent => $data) {
				$multiple = $data['multiple'] ?? false;

				if ($multiple) {
					$output[$type][$parent] = self::getItems(self::getFullPath($parent, $type, '*'), $data, $parent);
				} else {
					$output[$type][$parent] = self::getItem(self::getFullPath($parent, $type), $data, $parent);
				}
			}
		}

		return $output;
	}

	/**
	 * Get single item from the path.
	 *
	 * @param string $path Path to the item.
	 * @param array<mixed> $data Data array.
	 * @param string $parent Parent key.
	 *
	 * @throws InvalidManifest If manifest key is missing.
	 *
	 * @return array<string, mixed> Item.
	 */
	private static function getItem(string $path, array $data, string $parent): array
	{
		if (!\file_exists($path)) {
			return [];
		}

		$handle = \fopen($path, 'r');
		$file = \stream_get_contents($handle);

		if (!$file) {
			return [];
		}

		$fileDecoded = \json_decode($file, true);

		if (!$fileDecoded) {
			return [];
		}

		$autoset = $data['autoset'] ?? [];

		if ($autoset) {
			foreach ($autoset as $autosetItem) {
				$autosetItemKey = $autosetItem['key'] ?? '';
				$autosetItemValue = $autosetItem['value'] ?? '';
				$autosetItemParent = $autosetItem['parent'] ?? '';

				if (!$autosetItemKey) {
					continue;
				}

				// Handle the case where there is no parent.
				if (!$autosetItemParent) {
					if (!isset($fileDecoded[$autosetItemKey])) {
						$fileDecoded[$autosetItemKey] = $autosetItemValue;
					}
					continue;
				}

				// Handle the case where there is a parent.
				if (!isset($fileDecoded[$autosetItemParent][$autosetItemKey])) {
					$fileDecoded[$autosetItemParent][$autosetItemKey] = $autosetItemValue;
				}
			}
		}

		switch ($parent) {
			case AbstractManifestCache::BLOCKS_KEY:
				if (self::$blocksNamespace) {
					$fileDecoded['namespace'] = self::$blocksNamespace;
					$fileDecoded['blockFullName'] = self::$blocksNamespace . "/{$fileDecoded['blockName']}";
				}
				break;
			case AbstractManifestCache::SETTINGS_KEY:
				self::$blocksNamespace = $fileDecoded['namespace'] ?? '';
				break;
		}

		$validation = $data['validation'] ?? [];

		if ($validation) {
			foreach ($validation as $key) {
				if (!isset($fileDecoded[$key])) {
					throw InvalidManifest::missingManifestKeyException($key, $path);
				}
			}
		}

		return (array) $fileDecoded;
	}

	/**
	 * Get multiple items from the path.
	 *
	 * @param string $path Path to the items.
	 * @param array<mixed> $data Data array.
	 * @param string $parent Parent key.
	 *
	 * @return array<string, array<mixed>> Array of items.
	 */
	private static function getItems(string $path, array $data, string $parent): array
	{
		$output = [];

		$id = $data['id'] ?? '';

		foreach ((array)\glob($path) as $itemPath) {
			$item = self::getItem($itemPath, $data, $parent);

			$idName = $item[$id] ?? '';

			if (!$idName) {
				continue;
			}

			$output[$idName] = $item;
		}

		return $output;
	}

	/**
	 * Get full path.
	 *
	 * @param string $type Type of the item.
	 * @param string $cacheType Type of the cache.
	 * @param string $name Name of the item.
	 *
	 * @return string Full path.
	 */
	private static function getFullPath(string $type, string $cacheType, string $name = ''): string
	{
		$data = self::$cacheBuilder[$cacheType][$type] ?? [];

		if (!$data) {
			return '';
		}

		$path = $data['path'] ?? '';
		$fileName = $data['fileName'] ?? 'manifest.json';

		if (!$name) {
			return Helpers::getProjectPaths($path, [$fileName]);
		}

		return Helpers::getProjectPaths($path, [$name, $fileName]);
	}
}
