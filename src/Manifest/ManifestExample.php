<?php

/**
 * File containing an abstract class for holding Assets Manifest functionality.
 *
 * It is used to provide manifest.json file location used with Webpack to fetch correct file locations.
 *
 * @package EightshiftBoilerplate\Manifest
 */

declare(strict_types=1);

namespace EightshiftBoilerplate\Manifest;

use EightshiftLibs\Manifest\AbstractManifest;

/**
 * Class ManifestExample
 */
class ManifestExample extends AbstractManifest
{
	/**
	 * Register all hooks. Changed filter name to manifest.
	 *
	 * @return void
	 */
	public function register(): void
	{
		\add_action('init', [$this, 'setAssetsManifest']);
	}
}
