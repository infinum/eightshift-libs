<?php

/**
 * The file that defines a project config details like prefix, absolute path and etc.
 *
 * @package %namespace%\Cache
 */

declare(strict_types=1);

namespace %namespace%\Cache;

use %namespace%\Config\Config;
use %useLibs%\Cache\AbstractManifestCache;

/**
 * The project config class.
 */
class ManifestCacheExample extends AbstractManifestCache
{
	/**
	 * Get cache name.
	 *
	 * @return string Cache name.
	 */
	public function getCacheName(): string
	{
		return Config::getProjectTextDomain();
	}
}
