<?php

/**
 * Helpers for Project informaton.
 *
 * @package EightshiftLibs\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Helpers;

use EightshiftLibs\Exception\InvalidPath;

/**
 * Class ProjectInfoTrait Helper.
 */
trait ProjectInfoTrait
{
	/**
	 * Get the plugin version.
	 *
	 * @return string
	 */
	public static function getPluginVersion(): string
	{
		return static::getPluginDetails()['Version'] ?? \esc_html('1.0.0');
	}

	/**
	 * Get the plugin version.
	 *
	 * @return string
	 */
	public static function getPluginName(): string
	{
		return static::getPluginDetails()['Name'] ?? \esc_html('Plugin');
	}

	/**
	 * Get the plugin text domain.
	 *
	 * @return string
	 */
	public static function getPluginTextDomain(): string
	{
		return static::getPluginDetails()['TextDomain'] ?? \esc_html('PluginTextDomain');
	}

	/**
	 * Get the theme version.
	 *
	 * @return string
	 */
	public static function getThemeVersion(): string
	{
		return \wp_get_theme()->get('Version');
	}

	/**
	 * Get the theme name.
	 *
	 * @return string
	 */
	public static function getThemeName(): string
	{
		return \wp_get_theme()->get('Name');
	}

	/**
	 * Get the theme text domain.
	 *
	 * @return string
	 */
	public static function getThemeTextDomain(): string
	{
		return \wp_get_theme()->get('TextDomain');
	}

	/**
	 * Return projects absolute path.
	 *
	 * @param string $path Additional path to add to project path.
	 *
	 * @throws InvalidPath If the path is not readable.
	 *
	 * @return string
	 */
	public static function getProjectPath(string $path = ''): string
	{
		$fullPath = self::getProjectPaths('root') . \ltrim($path, \DIRECTORY_SEPARATOR);

		if (!\is_readable($fullPath)) {
			throw InvalidPath::missingDirectoryException($fullPath);
		}

		return $fullPath;
	}

	/**
	 * Get the plugin details.
	 *
	 * @return array<string, string>
	 */
	protected static function getPluginDetails(): array
	{
		if (!\function_exists('get_plugin_data')) {
			require_once(\ABSPATH . 'wp-admin/includes/plugin.php');
		}

		$path = self::getProjectPaths('pluginRoot');

		$name = \basename($path);

		return \get_plugin_data("{$path}{$name}.php");
	}
}
