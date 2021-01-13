<?php

/**
 * Project Config data interface.
 *
 * Used to define the way Config item is retrieved from the Config file.
 *
 * @package EightshiftLibs\Config
 */

declare(strict_types=1);

namespace EightshiftLibs\Config;

/**
 * Interface ConfigDataInterface
 */
interface ConfigDataInterface
{

	/**
	 * Method that returns project name.
	 *
	 * Generally used for naming assets handlers, languages, etc.
	 */
	public static function getProjectName(): string;

	/**
	 * Method that returns project version.
	 *
	 * Generally used for versioning asset handlers while enqueueing them.
	 */
	public static function getProjectVersion(): string;

	/**
	 * Return project absolute path.
	 *
	 * If used in a theme use get_template_directory() and in case it's used in a plugin use __DIR__.
	 *
	 * @param string $path Additional path to add to project path.
	 *
	 * @return string
	 */
	public static function getProjectPath(string $path = ''): string;
}
