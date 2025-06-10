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
	 * Cache for shouldCache() result to avoid repeated environment checks.
	 *
	 * @var bool|null
	 */
	private static ?bool $shouldCacheResult = null;

	/**
	 * Cache for file existence checks to avoid repeated filesystem operations.
	 *
	 * @var array<string, bool>
	 */
	private static array $fileExistsCache = [];

	/**
	 * Cache for file contents to avoid repeated file reads.
	 *
	 * @var array<string, string|false>
	 */
	private static array $fileContentsCache = [];

	/**
	 * Cache for JSON decoded results to avoid repeated parsing.
	 *
	 * @var array<string, array<mixed>|false>
	 */
	private static array $jsonDecodeCache = [];

	// -----------------------------------------------------
	// CACHE
	// -----------------------------------------------------

	/**
	 * Set cache details with optimized validation.
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
	 * @return void
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

		// Try to load from transient first.
		$transientData = \get_transient($transientKey);
		if ($transientData !== false && \is_array($transientData)) {
			// Validate timestamp consistency between transient and file.
			if (self::isTransientValid($cacheFile, $timestampKey)) {
				self::$cache = $transientData;
				return;
			}

			// Timestamp mismatch, remove invalid transient.
			\delete_transient($transientKey);
		}

		// Try to load from file cache.
		if (self::fileExistsCached($cacheFile)) {
			$content = self::getFileContentsCached($cacheFile);

			if ($content !== false) {
				$decoded = self::jsonDecodeCached($content);
				if ($decoded !== false) {
					// Update transient and timestamp from file cache.
					self::updateTransientCache($transientKey, $timestampKey, $decoded, $cacheFile);
					self::$cache = $decoded;
					return;
				}
			}
		}

		// Generate new cache data.
		$data = self::getAllManifests();

		// Write to file and update transient.
		if (self::writeFileOptimized($cacheFile, \wp_json_encode($data))) {
			self::updateTransientCache($transientKey, $timestampKey, $data, $cacheFile);
			self::$cache = $data;
		} else {
			// Fallback if file writing fails.
			self::$cache = $data;
		}
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
	 * Check if we should cache the service classes with optimized environment detection.
	 *
	 * @return bool
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
	 *
	 * @return string
	 */
	private static function getTransientKey(): string
	{
		return 'es_cache_' . \md5(self::$cacheName . self::$version);
	}

	/**
	 * Get timestamp key for version tracking.
	 *
	 * @return string
	 */
	private static function getTimestampKey(): string
	{
		return 'es_cache_stamp_' . \md5(self::$cacheName . self::$version);
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
		if (self::fileExistsCached($cacheFile)) {
			$fileTimestamp = \filemtime($cacheFile);
			if ($fileTimestamp === false) {
				return false;
			}

			// Compare timestamps.
			return (int) $storedTimestamp === (int) $fileTimestamp;
		}

		// If no file exists, transient is invalid.
		return false;
	}

	/**
	 * Update transient cache and timestamp in database.
	 *
	 * @param string $transientKey Transient key for cache storage.
	 * @param string $timestampKey Database option key for timestamp.
	 * @param array<mixed> $data Cache data to store.
	 * @param string $cacheFile Path to the cache file.
	 *
	 * @return void
	 */
	private static function updateTransientCache(
		string $transientKey,
		string $timestampKey,
		array $data,
		string $cacheFile
	): void {
		// Store data in transient (expire after 24 hours as fallback).
		\set_transient($transientKey, $data, \DAY_IN_SECONDS);

		// Update timestamp in database.
		if (self::fileExistsCached($cacheFile)) {
			$fileTimestamp = \filemtime($cacheFile);
			if ($fileTimestamp !== false) {
				\update_option($timestampKey, (int) $fileTimestamp, false);
			}
		}
	}

	/**
	 * Clear all cache layers (transient, file, and memory).
	 *
	 * @return void
	 */
	public static function clearAllCache(): void
	{
		// Clear memory cache.
		self::$cache = [];
		self::$fileExistsCache = [];
		self::$fileContentsCache = [];
		self::$jsonDecodeCache = [];

		// Clear transient cache.
		$transientKey = self::getTransientKey();
		\delete_transient($transientKey);

		// Clear timestamp option.
		$timestampKey = self::getTimestampKey();
		\delete_option($timestampKey);

		// Remove cache file.
		$cacheFile = Helpers::getEightshiftOutputPath('manifests.json');
		if (self::fileExistsCached($cacheFile)) {
			\unlink($cacheFile); // phpcs:ignore WordPress.WP.AlternativeFunctions.unlink_unlink
		}
	}

	/**
	 * Get all manifests from the paths with optimized processing.
	 *
	 * @return array<string, array<mixed>> Array of manifests.
	 */
	private static function getAllManifests(): array
	{
		// Early return for empty cache builder.
		if (empty(self::$cacheBuilder)) {
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

				if (!empty($result)) {
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
	 * @param array<mixed> $data Data array.
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
		if (!self::fileExistsCached($path)) {
			return [];
		}

		// Optimized file content reading.
		$fileContent = self::getFileContentsCached($path);
		if ($fileContent === false || $fileContent === '') {
			return [];
		}

		// Optimized JSON decoding.
		$fileDecoded = self::jsonDecodeCached($fileContent);
		if ($fileDecoded === false || !\is_array($fileDecoded)) {
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
	 * @param array<mixed> $data Configuration data.
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
	 * @param array<mixed> $data Configuration data.
	 * @param string $path File path for error reporting.
	 *
	 * @throws InvalidManifest If required key is missing.
	 *
	 * @return void
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
	 * @param array<mixed> $data Data array.
	 * @param string $parent Parent key.
	 *
	 * @return array<string, array<mixed>> Array of items.
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
			if (empty($item)) {
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
	 * Optimized file existence check with caching.
	 *
	 * @param string $path File path.
	 *
	 * @return bool Whether file exists.
	 */
	private static function fileExistsCached(string $path): bool
	{
		if (isset(self::$fileExistsCache[$path])) {
			return self::$fileExistsCache[$path];
		}

		$exists = \file_exists($path);

		// Cache result (limit cache size to prevent memory bloat).
		if (\count(self::$fileExistsCache) < 1000) {
			self::$fileExistsCache[$path] = $exists;
		}

		return $exists;
	}

	/**
	 * Optimized file content reading with caching and error handling.
	 *
	 * @param string $path File path.
	 *
	 * @return string|false File contents or false on failure.
	 */
	private static function getFileContentsCached(string $path)
	{
		if (isset(self::$fileContentsCache[$path])) {
			return self::$fileContentsCache[$path];
		}

		// Use file_get_contents for better performance than fopen/stream_get_contents.
		$content = \file_get_contents($path); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		// Cache result (limit cache size).
		if (\count(self::$fileContentsCache) < 500) {
			self::$fileContentsCache[$path] = $content;
		}

		return $content;
	}

	/**
	 * Optimized JSON decoding with caching and error handling.
	 *
	 * @param string $content JSON content.
	 *
	 * @return array<mixed>|false Decoded array or false on failure.
	 */
	private static function jsonDecodeCached(string $content)
	{
		// Use content hash as cache key to handle same content from different files.
		$cacheKey = \hash('xxh3', $content);

		if (isset(self::$jsonDecodeCache[$cacheKey])) {
			return self::$jsonDecodeCache[$cacheKey];
		}

		$decoded = \json_decode($content, true);

		// Only cache successful decodes.
		if (\json_last_error() === \JSON_ERROR_NONE && \is_array($decoded)) {
			// Cache result (limit cache size).
			if (\count(self::$jsonDecodeCache) < 500) {
				self::$jsonDecodeCache[$cacheKey] = $decoded;
			}
			return $decoded;
		}

		return false;
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
		if (!\is_dir($directory)) {
			if (!\mkdir($directory, 0755, true)) {
				return false;
			}
		}

		// Use LOCK_EX for atomic writes.
		$result = \file_put_contents($path, $content, \LOCK_EX); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents

		return $result !== false;
	}
}
