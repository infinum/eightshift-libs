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
// phpcs:ignore SlevomatCodingStandard.Namespaces.UnusedUses.UnusedUse
use Exception;

/**
 * Class CacheTrait Helper
 */
trait CacheTrait
{
	/**
	 * Cache.
	 *
	 * @var array<string, array<string, mixed>>
	 */
	private static $cache = [];

	/**
	 * Cache builder.
	 *
	 * @var array<string, array<string, array<string, mixed>>>
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
	 * Cache for shouldCache() result to avoid repeated environment checks.
	 *
	 * @var bool|null
	 */
	private static ?bool $shouldCacheResult = null;

	// -----------------------------------------------------
				// CACHE
				// -----------------------------------------------------
				/**
				 * Set cache details with optimized validation.
				 *
				 * @param array<string, array<string, array<string, mixed>>> $cacheBuilder Cache builder.
				 * @param string $cacheName Cache name.
				 * @param string $version Cache version.
				 */
	public static function setCacheDetails(
		array $cacheBuilder,
		string $cacheName,
		string $version
	): void {
		// Early return if already set with same values.
		if (
			self::$cacheBuilder === $cacheBuilder &&
			self::$cacheName === $cacheName &&
			self::$version === $version
		) {
			return;
		}

		self::$cacheBuilder = $cacheBuilder;
		self::$cacheName = $cacheName;
		self::$version = $version;

		Helpers::setAllCache();
	}

	/**
	 * Set internal cache with optimized file operations and transient support.
	 *
	 * @throws Exception If cache file is not valid.
	 *
	 * @return void
	 *
	 * phpcs:ignore Squiz.Commenting.FunctionCommentThrowTag.Missing
	 */
	public static function setAllCache(): void
	{
		// Early return if cache already set.
		if (!empty(self::$cache)) {
			return;
		}

		// Check if we should use caching at all.
		if (!self::shouldCache()) {
			self::$cache = self::getAllManifests();
			return;
		}

		$cacheFile = Helpers::getEightshiftOutputPath('manifests.json');
		$transientKey = self::getTransientKey();
		$timestampKey = self::getTimestampKey();

		// Fast path: try transient + file without locking.
		if (self::tryLoadFromCache($cacheFile, $transientKey, $timestampKey)) {
			return;
		}

		// Slow path: rebuild under an advisory lock to prevent stampede.
		$lockHandle = self::acquireRebuildLock($cacheFile);

		try {
			// Another request may have rebuilt while we were waiting on the lock.
			if (self::tryLoadFromCache($cacheFile, $transientKey, $timestampKey)) {
				return;
			}

			$data = self::getAllManifests();
			$encoded = \wp_json_encode($data);

			if (\is_string($encoded) && self::writeFileOptimized($cacheFile, $encoded)) {
				self::updateTransientCache($transientKey, $timestampKey, $encoded, $cacheFile);
			}

			self::$cache = $data;
		} finally {
			self::releaseRebuildLock($lockHandle);
		}
	}

	/**
	 * Try to populate the in-memory cache from transient or file. Returns true on hit.
	 *
	 * @phpstan-impure Result depends on external state (transient store, file mtime) that another process can change between calls.
	 *
	 * @param string $cacheFile Path to the cache file.
	 * @param string $transientKey Transient key.
	 * @param string $timestampKey Timestamp option key.
	 *
	 * @throws Exception If a stored payload fails to parse.
	 */
	private static function tryLoadFromCache(string $cacheFile, string $transientKey, string $timestampKey): bool
	{
		$transientData = \get_transient($transientKey);

		if ($transientData !== false && \is_string($transientData)) {
			if (self::isTransientValid($cacheFile, $timestampKey)) {
				self::$cache = self::parseManifest($transientData);
				return true;
			}

			// Timestamp mismatch, remove invalid transient.
			\delete_transient($transientKey);
		}

		if (\file_exists($cacheFile)) {
			$content = \file_get_contents($cacheFile); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			if ($content !== false) {
				$decoded = self::parseManifest($content);
				self::updateTransientCache($transientKey, $timestampKey, $content, $cacheFile);
				self::$cache = $decoded;
				return true;
			}
		}

		return false;
	}

	/**
	 * Acquire an exclusive advisory lock for the cache rebuild path.
	 *
	 * Returns the lock handle on success, or null when the lock can't be obtained
	 * (caller proceeds without stampede protection in that case).
	 *
	 * @param string $cacheFile Path to the cache file (lock lives next to it).
	 *
	 * @return resource|null
	 */
	private static function acquireRebuildLock(string $cacheFile)
	{
		$lockPath = $cacheFile . '.lock';
		$directory = \dirname($lockPath);

		if (!\is_dir($directory) && !\mkdir($directory, 0755, true) && !\is_dir($directory)) {
			return null;
		}

		$handle = \fopen($lockPath, 'c'); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
		if ($handle === false) {
			return null;
		}

		if (!\flock($handle, \LOCK_EX)) {
			\fclose($handle);
			return null;
		}

		return $handle;
	}

	/**
	 * Release a previously-acquired rebuild lock.
	 *
	 * @param resource|null $handle Lock handle returned by acquireRebuildLock().
	 */
	private static function releaseRebuildLock($handle): void
	{
		if (!\is_resource($handle)) {
			return;
		}

		\flock($handle, \LOCK_UN);
		\fclose($handle);
	}

	/**
	 * Get cache.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public static function getCache(): array
	{
		return self::$cache;
	}

	/**
	 * Get cache name.
	 */
	public static function getCacheName(): string
	{
		return self::$cacheName;
	}

	/**
	 * Check if we should cache the service classes with optimized environment detection.
	 */
	public static function shouldCache(): bool
	{
		// Return cached result if already computed.
		if (self::$shouldCacheResult !== null) {
			return self::$shouldCacheResult;
		}

		// Check WP_CLI first (fastest check).
		if (\defined('WP_CLI') && \WP_CLI) {
			self::$shouldCacheResult = false;
			return false;
		}

		// Check WP_ENVIRONMENT_TYPE with proper constant checking.
		if (\defined('WP_ENVIRONMENT_TYPE')) {
			$envType = \constant('WP_ENVIRONMENT_TYPE');
			if ($envType === 'development' || $envType === 'local') {
				self::$shouldCacheResult = false;
				return false;
			}
		}

		self::$shouldCacheResult = true;
		return true;
	}

	/**
	 * Get transient key for cache storage.
	 */
	private static function getTransientKey(): string
	{
		return 'es_cache_' . \md5(self::$cacheName);
	}

	/**
	 * Get timestamp key for version tracking.
	 */
	private static function getTimestampKey(): string
	{
		return 'es_cache_stamp_' . \md5(self::$cacheName);
	}

	/**
	 * Check if transient cache is valid by comparing timestamps.
	 *
	 * @param string $cacheFile Path to the cache file.
	 * @param string $timestampKey Database option key for timestamp.
	 *
	 * @return bool Whether the transient is valid.
	 */
	private static function isTransientValid(string $cacheFile, string $timestampKey): bool
	{
		// Get stored timestamp from database.
		$storedTimestamp = \get_option($timestampKey, 0);

		// Get file modification time if file exists.
		if (\file_exists($cacheFile)) {
			$fileTimestamp = \filemtime($cacheFile);
			if ($fileTimestamp === false) {
				return false;
			}

			// Compare timestamps.
			return (int) $storedTimestamp === $fileTimestamp;
		}

		// If no file exists, transient is invalid.
		return false;
	}

	/**
	 * Update transient cache and timestamp in database.
	 *
	 * @param string $transientKey Transient key for cache storage.
	 * @param string $timestampKey Database option key for timestamp.
	 * @param string $data Cache data to store.
	 * @param string $cacheFile Path to the cache file.
	 */
	private static function updateTransientCache(
		string $transientKey,
		string $timestampKey,
		string $data,
		string $cacheFile
	): void {
		// Store data in transient.
		\set_transient($transientKey, $data, 0);

		// Update timestamp in database.
		if (\file_exists($cacheFile)) {
			$fileTimestamp = \filemtime($cacheFile);
			if ($fileTimestamp !== false) {
				\update_option($timestampKey, $fileTimestamp, true);
			}
		}
	}

	/**
	 * Clear all cache layers (transient, file, and memory).
	 */
	public static function clearAllCache(): void
	{
		// Clear memory cache.
		self::$cache = [];

		// Clear transient cache.
		$transientKey = self::getTransientKey();
		\delete_transient($transientKey);

		// Clear timestamp option.
		$timestampKey = self::getTimestampKey();
		\delete_option($timestampKey);

		// Remove cache file.
		$cacheFile = Helpers::getEightshiftOutputPath('manifests.json');
		if (\file_exists($cacheFile)) {
			\unlink($cacheFile); // phpcs:ignore WordPress.WP.AlternativeFunctions.unlink_unlink
		}
	}

	/**
	 * Get all manifests from the paths with optimized processing.
	 *
	 * @return array<string, array<string, mixed>> Array of manifests.
	 */
	private static function getAllManifests(): array
	{
		// Early return for empty cache builder.
		if (self::$cacheBuilder === []) {
			return [];
		}

		$output = [];

		foreach (self::$cacheBuilder as $type => $items) {
			if (empty($items)) {
				continue;
			}

			$output[$type] = [];

			foreach ($items as $parent => $data) {
				if (empty($data)) {
					continue;
				}

				$multiple = $data['multiple'] ?? false;

				if ($multiple) {
					$result = self::getItems(self::getFullPath($parent, $type, '*'), $data, $parent);
				} else {
					$result = self::getItem(self::getFullPath($parent, $type), $data, $parent);
				}

				if ($result !== []) {
					$output[$type][$parent] = $result;
				}
			}
		}

		return $output;
	}

	/**
	 * Get single item from the path with optimized file operations and error handling.
	 *
	 * @param string $path Path to the item.
	 * @param array<string, mixed> $data Data array.
	 * @param string $parent Parent key.
	 *
	 * @throws InvalidManifest If manifest key is missing.
	 *
	 * @return array<string, mixed> Item.
	 */
	private static function getItem(string $path, array $data, string $parent): array
	{
		// Early return for empty path.
		if ($path === '') {
			return [];
		}

		// Optimized file existence check.
		if (!\file_exists($path)) {
			return [];
		}

		// Optimized file content reading.
		$fileContent = \file_get_contents($path); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		if ($fileContent === false || $fileContent === '') {
			return [];
		}

		// Optimized JSON decoding.
		try {
			$fileDecoded = self::parseManifest($fileContent);
		} catch (Exception) {
			return [];
		}

		// Process autoset configuration efficiently.
		$fileDecoded = self::processAutoset($fileDecoded, $data);

		// Handle specific parent cases efficiently.
		$fileDecoded = self::processParentSpecificLogic($fileDecoded, $parent);

		// Validate required keys efficiently.
		self::validateManifestKeys($fileDecoded, $data, $path);

		return $fileDecoded;
	}

	/**
	 * Process autoset configuration efficiently.
	 *
	 * @param array<string, mixed> $fileDecoded Decoded file data.
	 * @param array<string, mixed> $data Configuration data.
	 *
	 * @return array<string, mixed> Processed file data.
	 */
	private static function processAutoset(array $fileDecoded, array $data): array
	{
		$autoset = $data['autoset'] ?? [];

		if (empty($autoset)) {
			return $fileDecoded;
		}

		foreach ($autoset as $autosetItem) {
			if (empty($autosetItem)) {
				continue;
			}

			$key = $autosetItem['key'] ?? '';
			$value = $autosetItem['value'] ?? '';
			$parent = $autosetItem['parent'] ?? '';

			if ($key === '') {
				continue;
			}

			// Handle case with no parent.
			if ($parent === '') {
				if (!isset($fileDecoded[$key])) {
					$fileDecoded[$key] = $value;
				}
				continue;
			}

			// Handle case with parent.
			if (!isset($fileDecoded[$parent][$key])) {
				if (!isset($fileDecoded[$parent])) {
					$fileDecoded[$parent] = [];
				}
				$fileDecoded[$parent][$key] = $value;
			}
		}

		return $fileDecoded;
	}

	/**
	 * Process parent-specific logic efficiently.
	 *
	 * @param array<string, mixed> $fileDecoded Decoded file data.
	 * @param string $parent Parent type.
	 *
	 * @return array<string, mixed> Processed file data.
	 */
	private static function processParentSpecificLogic(array $fileDecoded, string $parent): array
	{
		switch ($parent) {
			case AbstractManifestCache::BLOCKS_KEY:
				if (self::$blocksNamespace !== '') {
					$fileDecoded['namespace'] = self::$blocksNamespace;
					$blockName = $fileDecoded['blockName'] ?? '';
					if ($blockName !== '') {
						$fileDecoded['blockFullName'] = self::$blocksNamespace . "/{$blockName}";
					}
				}
				break;
			case AbstractManifestCache::SETTINGS_KEY:
				self::$blocksNamespace = $fileDecoded['namespace'] ?? '';
				break;
		}

		return $fileDecoded;
	}

	/**
	 * Validate manifest keys efficiently.
	 *
	 * @param array<string, mixed> $fileDecoded Decoded file data.
	 * @param array<string, mixed> $data Configuration data.
	 * @param string $path File path for error reporting.
	 *
	 * @throws InvalidManifest If required key is missing.
	 */
	private static function validateManifestKeys(array $fileDecoded, array $data, string $path): void
	{
		$validation = $data['validation'] ?? [];

		if (empty($validation)) {
			return;
		}

		foreach ($validation as $key) {
			if (!isset($fileDecoded[$key])) {
				throw InvalidManifest::missingManifestKeyException($key, $path);
			}
		}
	}

	/**
	 * Get multiple items from the path with optimized processing.
	 *
	 * @param string $path Path to the items.
	 * @param array<string, mixed> $data Data array.
	 * @param string $parent Parent key.
	 *
	 * @return array<string, array<string, mixed>> Array of items.
	 */
	private static function getItems(string $path, array $data, string $parent): array
	{
		// Early return for empty path.
		if ($path === '') {
			return [];
		}

		$output = [];
		$id = $data['id'] ?? '';

		// Early return if no ID specified.
		if ($id === '') {
			return [];
		}

		// Use optimized glob with error handling.
		$globResults = \glob($path);
		if ($globResults === false) {
			return [];
		}

		foreach ($globResults as $itemPath) {
			if ($itemPath === '') {
				continue;
			}

			$item = self::getItem($itemPath, $data, $parent);
			if ($item === []) {
				continue;
			}

			$idName = $item[$id] ?? '';
			if ($idName === '') {
				continue;
			}

			$output[$idName] = $item;
		}

		return $output;
	}

	/**
	 * Get full path with optimized string building.
	 *
	 * @param string $type Type of the item.
	 * @param string $cacheType Type of the cache.
	 * @param string $name Name of the item.
	 *
	 * @return string Full path.
	 */
	private static function getFullPath(string $type, string $cacheType, string $name = ''): string
	{
		// Early return for empty inputs.
		if ($type === '' || $cacheType === '') {
			return '';
		}

		$data = self::$cacheBuilder[$cacheType][$type] ?? [];
		if (empty($data)) {
			return '';
		}

		$path = $data['path'] ?? '';
		$fileName = $data['fileName'] ?? 'manifest.json';

		// Early return for empty path.
		if ($path === '') {
			return '';
		}

		if ($name === '') {
			return Helpers::getProjectPaths($path, [$fileName]);
		}

		return Helpers::getProjectPaths($path, [$name, $fileName]);
	}

	/**
	 * Optimized file writing with error handling.
	 *
	 * @param string $path File path.
	 * @param string $content Content to write.
	 *
	 * @return bool Success status.
	 */
	private static function writeFileOptimized(string $path, string $content): bool
	{
		// Ensure directory exists.
		$directory = \dirname($path);
		if (!\is_dir($directory) && !\mkdir($directory, 0755, true)) {
			return false;
		}

		// Use LOCK_EX for atomic writes.
		$result = \file_put_contents($path, $content, \LOCK_EX); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents

		return $result !== false;
	}
}
