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
			\trailingslashit(dirname(__DIR__, 5)) . $path,
			\trailingslashit(\get_stylesheet_directory()) . $path,
			\trailingslashit(\get_template_directory()) . $path,
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
