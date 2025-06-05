<?php

/**
 * Helpers for project info.
 *
 * @package EightshiftLibs\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Helpers;

/**
 * Class ProjectInfo Helper
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
		return self::getPluginDetails()['Version'] ?? \esc_html('1.0.0');
	}

	/**
	 * Get the plugin version.
	 *
	 * @return string
	 */
	public static function getPluginName(): string
	{
		return self::getPluginDetails()['Name'] ?? \esc_html('Plugin');
	}

	/**
	 * Get the plugin text domain.
	 *
	 * @return string
	 */
	public static function getPluginTextDomain(): string
	{
		return self::getPluginDetails()['TextDomain'] ?? \esc_html('PluginTextDomain');
	}

	/**
	 * Get the theme version.
	 *
	 * @return string
	 */
	public static function getThemeVersion(): string
	{
		return \wp_get_theme()->get('Version') ?: ''; // phpcs:ignore WordPress.PHP.DisallowShortTernary.Found
	}

	/**
	 * Get the theme name.
	 *
	 * @return string
	 */
	public static function getThemeName(): string
	{
		return \wp_get_theme()->get('Name') ?: ''; // phpcs:ignore WordPress.PHP.DisallowShortTernary.Found
	}

	/**
	 * Get the theme text domain.
	 *
	 * @return string
	 */
	public static function getThemeTextDomain(): string
	{
		return \wp_get_theme()->get('TextDomain') ?: ''; // phpcs:ignore WordPress.PHP.DisallowShortTernary.Found
	}

	/**
	 * Get the plugin details.
	 *
	 * @return array<mixed>
	 */
	protected static function getPluginDetails(): array
	{
		if (!\function_exists('get_plugin_data')) {
			require_once(\ABSPATH . 'wp-admin/includes/plugin.php'); // @phpstan-ignore-line
		}

		$path = Helpers::getProjectPaths('projectRoot');

		$name = \basename($path);

		return \get_plugin_data("{$path}{$name}.php", false, false);
	}
}
