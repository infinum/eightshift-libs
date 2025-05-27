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
	 * Namespace for blocks.
	 *
	 * @var string
	 */
	private static $blocksNamespace = '';

	/**
	 * Cache file name.
	 *
	 * @var string
	 */
	private static $cacheFileName = '';

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
		self::$cacheFileName = \str_replace(' ', '', "{$cacheName}Manifests{$version}.json");

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

		$cacheFile = Helpers::getEightshiftOutputPath(self::$cacheFileName);

		if (\file_exists($cacheFile)) {
			$handle = \fopen($cacheFile, 'r');
			$output = \stream_get_contents($handle);

			self::$cache = \json_decode($output, true);
			return;
		}

		$data = self::getAllManifests();
		\file_put_contents($cacheFile, \wp_json_encode($data)); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
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
