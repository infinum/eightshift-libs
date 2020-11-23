<?php

/**
 * Class that adds ThemeOptionsExample capability.
 *
 * @package EightshiftBoilerplate\ThemeOptions
 */

declare(strict_types=1);

namespace EightshiftBoilerplate\ThemeOptions;

use EightshiftLibs\Services\ServiceInterface;

/**
 * Class ThemeOptionsExample
 */
class ThemeOptionsExample implements ServiceInterface
{

	/**
	 * Theme options api page slug
	 *
	 * @var string
	 */
	public const THEME_OPTIONS_SLUG = 'theme-options';

	/**
	 * Theme Options ACF Capability Name.
	 */
	public const THEME_OPTIONS_CAPABILITY = 'manage_acf_theme_options';

	/**
	 * Register all the hooks
	 */
	public function register(): void
	{
		add_action('acf/init', [$this, 'createThemeOptionsPage']);
		add_action('acf/init', [$this, 'registerThemeOptions']);
	}

	/**
	 * Create Options page in sidebar
	 *
	 * @return void
	 */
	public function createThemeOptionsPage(): void
	{
		if (function_exists('acf_add_options_page')) {
			\acf_add_options_page(
				[
					'page_title' => esc_html__('General Settings', 'eightshift-libs'),
					'menu_title' => esc_html__('Theme Options', 'eightshift-libs'),
					'menu_slug'  => static::THEME_OPTIONS_SLUG,
					'capability' => static::THEME_OPTIONS_CAPABILITY,
					'redirect'   => false,
					'icon_url'   => 'dashicons-welcome-view-site',
					'autoload'   => true,
				]
			);
		}
	}

	/**
	 * Populate Options page
	 *
	 * @return void
	 */
	public function registerThemeOptions(): void
	{
		if (function_exists('acf_add_options_page')) {
			acf_add_local_field_group();
		}
	}
}
