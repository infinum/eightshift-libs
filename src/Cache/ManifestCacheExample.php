<?php

/**
 * The file that defines a project config details like prefix, absolute path and etc.
 *
 * @package %g_namespace%\Cache
 */

declare(strict_types=1);

namespace %g_namespace%\Cache;

use %g_namespace%\Config\Config;
use %g_use_libs%\Cache\AbstractManifestCache;

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
