<?php

/**
 * Class that adds ThemeOptionsExample capability.
 *
 * @package %namespace%\ThemeOptions
 */

declare(strict_types=1);

namespace %namespace%\ThemeOptions;

use %useLibs%\Services\ServiceInterface;

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
	public const THEME_OPTIONS_CAPABILITY = 'edit_theme_options';

	/**
	 * Register all the hooks
	 */
	public function register(): void
	{
		// Silently exit if no ACF is installed.
		if (!\class_exists('ACF')) {
			return;
		}

		\add_action('acf/init', [$this, 'createThemeOptionsPage']);
		\add_action('acf/init', [$this, 'registerThemeOptions']);
	}

	/**
	 * Create Options page in sidebar
	 *
	 * @return void
	 */
	public function createThemeOptionsPage(): void
	{
		if (\function_exists('acf_add_options_page')) {
			\acf_add_options_page(
				[
					'page_title' => \esc_html__('General Settings', 'eightshift-libs'),
					'menu_title' => \esc_html__('Theme Options', 'eightshift-libs'),
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
		if (\function_exists('acf_add_local_field_group')) {
			\acf_add_local_field_group(
				[
					'key' => 'group_5fcab51c7138c',
					'title' => \esc_html__('Theme Options', 'eightshift-libs'),
					'fields' => [],
					'location' => [
						[
							[
								'param' => 'options_page',
								'operator' => '==',
								'value' => static::THEME_OPTIONS_SLUG,
							],
						],
					],
					'menu_order' => 20,
					'position' => 'normal',
					'style' => 'default',
					'label_placement' => 'top',
					'instruction_placement' => 'label',
					'hide_on_screen' => '',
					'active' => true,
					'description' => '',
				]
			);
		}
	}
}
