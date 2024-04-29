<?php

/**
 * The Theme/Frontend Enqueue specific functionality.
 *
 * @package EightshiftBoilerplate\Enqueue\Theme
 */

declare(strict_types=1);

namespace EightshiftBoilerplate\Enqueue\Theme;

use EightshiftLibs\Enqueue\Theme\AbstractEnqueueTheme;
use EightshiftLibs\Helpers\Helpers;

/**
 * Class EnqueueThemeExample
 */
class EnqueueThemeExample extends AbstractEnqueueTheme
{
	/**
	 * Register all the hooks
	 *
	 * @return void
	 */
	public function register(): void
	{
		\add_action('wp_enqueue_scripts', [$this, 'enqueueStyles'], 10);
		\add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
	}

	/**
	 * Method that returns assets name used to prefix asset handlers.
	 *
	 * @return string
	 */
	public function getAssetsPrefix(): string
	{
		return Helpers::getThemeName();
	}

	/**
	 * Method that returns assets version for versioning asset handlers.
	 *
	 * @return string
	 */
	public function getAssetsVersion(): string
	{
		return Helpers::getThemeVersion();
	}
}
