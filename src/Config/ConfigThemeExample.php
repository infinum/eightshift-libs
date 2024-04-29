<?php

/**
 * The file that defines the project entry point class.
 *
 * A class definition that includes attributes and functions used across both the
 * public side of the site and the admin area.
 *
 * @package EightshiftBoilerplate\Config
 */

declare(strict_types=1);

namespace EightshiftBoilerplate\Config;

use EightshiftLibs\Config\AbstractConfig;

/**
 * The project config class.
 */
class ConfigThemeExample extends AbstractConfig
{
	/**
	 * Method that returns project name.
	 *
	 * Generally used for naming assets handlers, languages, etc.
	 */
	public static function getProjectName(): string
	{
		return self::getThemeName();
	}

	/**
	 * Method that returns project version.
	 *
	 * Generally used for versioning asset handlers while enqueueing them.
	 */
	public static function getProjectVersion(): string
	{
		return self::getThemeVersion();
	}

	/**
	 * Method that returns project text domain.
	 *
	 * Generally used for caching and translations.
	 */
	public static function getProjectTextDomain(): string
	{
		return self::getThemeTextDomain();
	}
}
