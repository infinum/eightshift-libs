<?php

/**
 * The file that defines a manifest cache interface.
 *
 * This interface is used to define the methods that are used in the manifest cache class.
 *
 * @package EightshiftLibs\Cache
 */

declare(strict_types=1);

namespace EightshiftLibs\Cache;

interface ManifestCacheInterface
{
	/**
	 * Get manifest cache top item.
	 *
	 * @param string $key Key of the cache.
	 *
	 * @return array<string, mixed> Array of cache item.
	 */
	public function getManifestCacheTopItem(string $key): array;

		/**
	 * Get manifest cache subitem.
	 *
	 * @param string $key Key of the cache.
	 * @param string $path Path of the cache.
	 *
	 * @return array<string, mixed> Array of cache item.
	 */
	public function getManifestCacheSubItem(string $key, string $path): array;
}
