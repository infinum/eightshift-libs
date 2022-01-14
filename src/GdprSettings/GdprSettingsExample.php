<?php

/**
 * Class that adds GdprSettingsExample capability.
 *
 * @package EightshiftBoilerplate\GdprSettings
 */

declare(strict_types=1);

namespace EightshiftBoilerplate\GdprSettings;

use EightshiftLibs\Services\ServiceInterface;

/**
 * GdprSettingsExample class.
 */
class GdprSettingsExample implements ServiceInterface
{
	public const GDPR_SLUG = 'gdpr-settings';

	public const GROUP_GENERAL = 'general';
	public const GROUP_REQUIRED_LEVEL = 'required_level';
	public const GROUP_FUNCTIONAL_LEVEL = 'functional_level';
	public const GROUP_MARKETING_LEVEL = 'marketing_level';

	public const BASIC_MODAL_TITLE = 'basic_modal_title';
	public const BASIC_MODAL_INTRO = 'basic_modal_intro';
	public const REQUIRED_LEVEL_TITLE = 'required_level_title';
	public const REQUIRED_LEVEL_INTRO = 'required_level_intro';
	public const FUNCTIONAL_LEVEL_TITLE = 'functional_level_title';
	public const FUNCTIONAL_LEVEL_INTRO = 'functional_level_intro';
	public const MARKETING_LEVEL_TITLE = 'marketing_level_title';
	public const MARKETING_LEVEL_INTRO = 'marketing_level_intro';
	public const ADDITIONAL_LINK = 'additional_link';

	/**
	 * GDPR Settings ACF Capability Name.
	 */
	public const GDPR_SETTINGS_CAPABILITY = 'edit_theme_options';

	/**
	 * Register all the hooks.
	 *
	 * @return void
	 */
	public function register(): void
	{
		// Silently exit if no ACF is installed.
		if (!class_exists('ACF')) {
			return;
		}

		add_action('acf/init', [$this, 'createGdprSettingsPage'], 20);
		add_action('acf/init', [$this, 'registerGdprSettings'], 20);
	}

	/**
	 * Create a GDPR Settings page.
	 */
	public function createGdprSettingsPage(): void
	{
		\acf_add_options_page(
			[
				'page_title' => \esc_html__('GDPR Settings', 'eightshift-libs'),
				'menu_title' => \esc_html__('GDPR Settings', 'eightshift-libs'),
				'menu_slug'  => static::GDPR_SLUG,
				'capability' => static::GDPR_SETTINGS_CAPABILITY,
				'redirect'   => false,
				'icon_url'   => 'dashicons-clipboard',
				'autoload'   => true,
			]
		);
	}

	/**
	 * Register the GDPR settings.
	 */
	public function registerGdprSettings(): void
	{
		\acf_add_local_field_group(
			[
				'key' => 'group_6013c5def31e8',
				'title' => esc_html__('GDPR Settings', 'eightshift-libs'),
				'fields' => [
					[
						'key' => 'field_6013c6aa2370d',
						'label' => esc_html__('General', 'eightshift-libs'),
						'name' => self::GROUP_GENERAL,
						'type' => 'group',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => [
							'width' => '',
							'class' => '',
							'id' => '',
						],
						'layout' => 'block',
						'sub_fields' => [
							[
								'key' => 'field_6013c5fd23709',
								'label' => esc_html__('Basic modal title', 'eightshift-libs'),
								'name' => self::BASIC_MODAL_TITLE,
								'type' => 'text',
								'instructions' => '',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => [
									'width' => '',
									'class' => '',
									'id' => '',
								],
								'default_value' => '',
								'placeholder' => '',
								'prepend' => '',
								'append' => '',
								'maxlength' => '',
							],
							[
								'key' => 'field_6013c6582370a',
								'label' => esc_html__('Basic modal intro', 'eightshift-libs'),
								'name' => self::BASIC_MODAL_INTRO,
								'type' => 'textarea',
								'instructions' => esc_html__('Use {%manage_link|Link text%} placeholder to correctly link the manage cookie link on the frontend.', 'eightshift-libs'), // phpcs:ignore Generic.Files.LineLength.TooLong
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => [
									'width' => '',
									'class' => '',
									'id' => '',
								],
								'default_value' => '',
								'placeholder' => '',
								'maxlength' => '',
								'rows' => '',
								'new_lines' => 'br',
							],
						],
					],
					[
						'key' => 'field_6013c6e52370e',
						'label' => esc_html__('Required Level', 'eightshift-libs'),
						'name' => self::GROUP_REQUIRED_LEVEL,
						'type' => 'group',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => [
							'width' => '',
							'class' => '',
							'id' => '',
						],
						'layout' => 'block',
						'sub_fields' => [
							[
								'key' => 'field_6013c6e52370f',
								'label' => esc_html__('Required level title', 'eightshift-libs'),
								'name' => self::REQUIRED_LEVEL_TITLE,
								'type' => 'text',
								'instructions' => '',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => [
									'width' => '',
									'class' => '',
									'id' => '',
								],
								'default_value' => '',
								'placeholder' => '',
								'prepend' => '',
								'append' => '',
								'maxlength' => '',
							],
							[
								'key' => 'field_6013c6e523710',
								'label' => esc_html__('Required level intro', 'eightshift-libs'),
								'name' => self::REQUIRED_LEVEL_INTRO,
								'type' => 'textarea',
								'instructions' => '',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => [
									'width' => '',
									'class' => '',
									'id' => '',
								],
								'default_value' => '',
								'placeholder' => '',
								'maxlength' => '',
								'rows' => '',
								'new_lines' => 'br',
							],
						],
					],
					[
						'key' => 'field_6013c76023712',
						'label' => esc_html__('Functional Level', 'eightshift-libs'),
						'name' => self::GROUP_FUNCTIONAL_LEVEL,
						'type' => 'group',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => [
							'width' => '',
							'class' => '',
							'id' => '',
						],
						'layout' => 'block',
						'sub_fields' => [
							[
								'key' => 'field_6013c76023713',
								'label' => esc_html__('Functional level title', 'eightshift-libs'),
								'name' => self::FUNCTIONAL_LEVEL_TITLE,
								'type' => 'text',
								'instructions' => '',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => [
									'width' => '',
									'class' => '',
									'id' => '',
								],
								'default_value' => '',
								'placeholder' => '',
								'prepend' => '',
								'append' => '',
								'maxlength' => '',
							],
							[
								'key' => 'field_6013c76023715',
								'label' => esc_html__('Functional level intro', 'eightshift-libs'),
								'name' => self::FUNCTIONAL_LEVEL_INTRO,
								'type' => 'textarea',
								'instructions' => '',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => [
									'width' => '',
									'class' => '',
									'id' => '',
								],
								'default_value' => '',
								'placeholder' => '',
								'maxlength' => '',
								'rows' => '',
								'new_lines' => 'br',
							],
						],
					],
					[
						'key' => 'field_6013c7a123716',
						'label' => esc_html__('Marketing Level', 'eightshift-libs'),
						'name' => self::GROUP_MARKETING_LEVEL,
						'type' => 'group',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => [
							'width' => '',
							'class' => '',
							'id' => '',
						],
						'layout' => 'block',
						'sub_fields' => [
							[
								'key' => 'field_6013c7a123717',
								'label' => esc_html__('Marketing level title', 'eightshift-libs'),
								'name' => self::MARKETING_LEVEL_TITLE,
								'type' => 'text',
								'instructions' => '',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => [
									'width' => '',
									'class' => '',
									'id' => '',
								],
								'default_value' => '',
								'placeholder' => '',
								'prepend' => '',
								'append' => '',
								'maxlength' => '',
							],
							[
								'key' => 'field_6013c7a123719',
								'label' => esc_html__('Marketing level intro', 'eightshift-libs'),
								'name' => self::MARKETING_LEVEL_INTRO,
								'type' => 'textarea',
								'instructions' => '',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => [
									'width' => '',
									'class' => '',
									'id' => '',
								],
								'default_value' => '',
								'placeholder' => '',
								'maxlength' => '',
								'rows' => '',
								'new_lines' => 'br',
							],
						],
					],
				],
				'location' => [
					[
						[
							'param' => 'options_page',
							'operator' => '==',
							'value' => self::GDPR_SLUG,
						],
					],
				],
				'menu_order' => 0,
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
