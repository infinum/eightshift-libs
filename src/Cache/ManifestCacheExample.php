<?php

/**
 * The file that defines a project config details like prefix, absolute path and etc.
 *
 * @package EightshiftBoilerplate\Cache
 */

declare(strict_types=1);

namespace EightshiftBoilerplate\Cache;

use EightshiftBoilerplate\Config\Config;
use EightshiftLibs\Cache\AbstractManifestCache;

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
