<?php

/**
 * The Language specific functionality.
 *
 * @package EightshiftBoilerplate\I18n
 */

declare(strict_types=1);

namespace EightshiftBoilerplate\I18n;

use EightshiftBoilerplate\Config\Config;
use EightshiftLibs\Services\ServiceInterface;

/**
 * Class i18n
 *
 * This class handles theme or admin languages.
 */
class I18n implements ServiceInterface
{

	/**
	 * Register all the hooks
	 *
	 * @return void
	 */
	public function register(): void
	{
		\add_action('after_setup_theme', [$this, 'loadThemeTextdomain']);
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @return void
	 */
	public function loadThemeTextdomain(): void
	{
		\load_theme_textdomain(
			Config::getProjectName(),
			Config::getProjectPath('/src/I18n')
		);
	}
}
