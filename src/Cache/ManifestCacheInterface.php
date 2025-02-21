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
	 * Set all cache.
	 *
	 * @return void
	 */
	public function setAllCache(): void;
}
