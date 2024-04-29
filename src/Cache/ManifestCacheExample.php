<?php

/**
 * The file that defines a project config details like prefix, absolute path and etc.
 *
 * @package EightshiftBoilerplate\Cache
 */

declare(strict_types=1);

namespace EightshiftBoilerplate\Cache;

use EightshiftLibs\Cache\AbstractManifestCache;
use EightshiftLibs\Helpers\Helpers;

/**
 * The project config class.
 */
class ManifestCache extends AbstractManifestCache
{
	/**
	 * Get cache name.
	 *
	 * @return string Cache name.
	 */
	public function getCacheName(): string
	{
		return Helpers::getThemeTextDomain();
	}
}
