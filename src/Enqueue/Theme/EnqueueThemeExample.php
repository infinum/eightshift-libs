<?php

/**
 * The Theme/Frontend Enqueue specific functionality.
 *
 * @package %g_namespace%\Enqueue\Theme
 */

declare(strict_types=1);

namespace %g_namespace%\Enqueue\Theme;

use %g_namespace%\Config\Config;
use %g_use_libs%\Enqueue\Theme\AbstractEnqueueTheme;

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
		return Config::getProjectTextDomain();
	}

	/**
	 * Method that returns assets version for versioning asset handlers.
	 *
	 * @return string
	 */
	public function getAssetsVersion(): string
	{
		return Config::getProjectVersion();
	}
}
