<?php

/**
 * The file that defines a project config details like prefix, absolute path and etc.
 *
 * @package EightshiftLibs\Config
 */

declare(strict_types=1);

namespace EightshiftLibs\Config;

use EightshiftLibs\Exception\InvalidPath;

/**
 * The project config class.
 */
abstract class AbstractConfigData implements ConfigDataInterface
{

	/**
	 * Method that returns every string prefixed with project prefix based on project type.
	 * It converts all spaces and "_" with "-", also it converts all characters to lowercase.
	 *
	 * @param string $key String key to append prefix on.
	 *
	 * @return string Returns key prefixed with project prefix.
	 */
	public static function getConfig(string $key): string
	{
		$projectPrefix = static::getProjectPrefix();
		$projectPrefix = str_replace(' ', '-', $projectPrefix);
		$projectPrefix = str_replace('_', '-', $projectPrefix);
		$projectPrefix = strtolower($projectPrefix);

		return "{$projectPrefix}-{$key}";
	}

	/**
	 * Return project absolute path.
	 *
	 * If used in a theme use get_template_directory() and in case it's used in a plugin use __DIR__.
	 *
	 * @param string $path Additional path to add to project path.
	 *
	 * @throws InvalidPath If an invalid URI was passed.
	 *
	 * @return string Valid URI.
	 */
	public static function getProjectPath(string $path = ''): string
	{
		$locations = [
			\trailingslashit(\get_stylesheet_directory()) . $path,
			\trailingslashit(\get_template_directory()) . $path,
			\trailingslashit(__DIR__) . $path,
		];

		foreach ($locations as $location) {
			if (is_readable($location)) {
				return $location;
			}
		}

		if (!is_readable($path)) {
			throw InvalidPath::fromUri($path);
		}

		return $path;
	}
}
