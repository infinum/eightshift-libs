<?php

/**
 * The Media specific functionality.
 *
 * @package EightshiftBoilerplate\Media
 */

declare(strict_types=1);

namespace EightshiftBoilerplate\Media;

use EightshiftLibs\Media\AbstractMedia;

/**
 * Class MediaExample
 *
 * This class handles all media options. Sizes, Types, Features, etc.
 */
class MediaExample extends AbstractMedia
{

	/**
	 * Register all the hooks
	 *
	 * @return void
	 */
	public function register(): void
	{
		\add_action('after_setup_theme', [$this, 'addThemeSupport'], 20);
	}
}
