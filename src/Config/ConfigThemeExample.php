<?php

/**
 * The file that defines the project entry point class.
 *
 * A class definition that includes attributes and functions used across both the
 * public side of the site and the admin area.
 *
 * @package %g_namespace%\Config
 */

declare(strict_types=1);

namespace %g_namespace%\Config;

use %g_use_libs%\Helpers\Helpers;

/**
 * The project config class.
 */
class ConfigThemeExample
{
	/**
	 * Method that returns project name.
	 *
	 * Generally used for naming assets handlers, languages, etc.
	 *
	 * @return string Project name.
	 */
	public static function getProjectName(): string
	{
		return Helpers::getThemeName();
	}

	/**
	 * Method that returns project version.
	 *
	 * Generally used for versioning asset handlers while enqueueing them.
	 *
	 * @return string Project version.
	 */
	public static function getProjectVersion(): string
	{
		return Helpers::getThemeVersion();
	}

	/**
	 * Method that returns project text domain.
	 *
	 * Generally used for caching and translations.
	 *
	 * @return string Project text domain.
	 */
	public static function getProjectTextDomain(): string
	{
		return Helpers::getThemeTextDomain();
	}

	/**
	 * Method that returns project REST-API namespace.
	 *
	 * Used for namespacing projects REST-API routes and fields.
	 *
	 * @return string Project name.
	 */
	public static function getProjectRoutesNamespace(): string
	{
		return self::getProjectName();
	}

	/**
	 * Method that returns project REST-API version.
	 *
	 * Used for versioning projects REST-API routes and fields.
	 *
	 * @return string Project route version.
	 */
	public static function getProjectRoutesVersion(): string
	{
		return 'v1';
	}
}
