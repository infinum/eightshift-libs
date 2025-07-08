<?php

/**
 * The Language specific functionality.
 *
 * @package %g_namespace%\I18n
 */

declare(strict_types=1);

namespace %g_namespace%\I18n;

use %g_namespace%\Config\Config;
use %g_use_libs%\Helpers\Helpers;
use %g_use_libs%\Services\ServiceInterface;

/**
 * Class i18n
 *
 * This class handles theme or admin languages.
 */
class I18nPluginExample implements ServiceInterface
{
	/**
	 * Register all the hooks
	 *
	 * @return void
	 */
	public function register(): void
	{
		\add_action('load_plugin_textdomain', [$this, 'loadThemeTextdomain'], 20);
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @return void
	 */
	public function loadThemeTextdomain(): void
	{
		$sep = \DIRECTORY_SEPARATOR;
		\load_theme_textdomain(
			Config::getProjectName(),
			Helpers::getProjectPaths('src', ['I18n', 'languages'])
		);
	}
}
