<?php

/**
 * File containing an interface for holding Manifest Cache functionality.
 *
 * It is used to provide manifest.json file location stored in the transient cache.
 *
 * @package EightshiftLibs\Cache
 */

declare(strict_types=1);

namespace EightshiftLibs\Cache;

/**
 * Interface ManifestCacheInterface
 */
interface ManifestCacheInterface
{
	/**
	 * Get manifest cache top item.
	 *
	 * @param string $key Key of the cache.
	 * @param string $cacheType Type of the cache.
	 *
	 * @return array<string, mixed> Array of cache item.
	 */
	public function getManifestCacheTopItem(string $key, string $cacheType = AbstractManifestCache::TYPE_BLOCKS): array;

	/**
	 * Get manifest cache subitem.
	 *
	 * @param string $key Key of the cache.
	 * @param string $path Path of the cache.
	 * @param string $cacheType Type of the cache.
	 *
	 * @return array<string, mixed> Array of cache item.
	 */
	public function getManifestCacheSubItem(string $key, string $path, string $cacheType = AbstractManifestCache::TYPE_BLOCKS): array;
}
