<?php

/**
 * The Media specific functionality.
 *
 * @package EightshiftBoilerplate\Media
 */

declare(strict_types=1);

namespace EightshiftBoilerplate\Media;

use EightshiftLibs\Services\ServiceInterface;

/**
 * Class MediaExample
 *
 * This class handles all media options. Sizes, Types, Features, etc.
 */
class MediaExample implements ServiceInterface
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


	/**
	 * Enable theme support
	 *
	 * For full list check: https://developer.wordpress.org/reference/functions/add_theme_support/
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
