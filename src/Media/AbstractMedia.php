<?php

/**
 * The Media specific functionality.
 *
 * @package EightshiftLibs\Media
 */

declare(strict_types=1);

namespace EightshiftLibs\Media;

use EightshiftLibs\Services\ServiceInterface;

/**
 * Class Media
 *
 * This class handles all media options. Sizes, Types, Features, etc.
 */
abstract class AbstractMedia implements ServiceInterface
{

	/**
	 * Enable theme support
	 * for full list check: https://developer.wordpress.org/reference/functions/add_theme_support/
	 *
	 * @return void
	 */
	public function addThemeSupport(): void
	{
		\add_theme_support('title-tag');
		\add_theme_support('html5');
		\add_theme_support('post-thumbnails');
	}
}
