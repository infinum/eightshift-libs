<?php

/**
 * The Language specific functionality.
 *
 * @package EightshiftLibs\I18n
 */

declare(strict_types=1);

namespace EightshiftLibs\I18n;

use EightshiftLibs\Config\Config;

/**
 * Class i18n
 *
 * This class handles theme or admin languages.
 */
class I18n extends AbstractI18n
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
	 * Text domain. Unique identifier for retrieving translated strings.
	 *
	 * @return string
	 */
	public function getTextdomainName(): string
	{
		return Config::getProjectName();
	}

	/**
	 * Path to the directory containing the .mo file.
	 *
	 * @return string
	 */
	public function getTranslationFilePath(): string
	{
		return Config::getProjectPath('/src/I18n');
	}
}
