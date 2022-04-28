<?php

/**
 * Class that adds Analytics and GDPR capability.
 *
 * @package EightshiftBoilerplate\AnalyticsGdpr
 */

declare(strict_types=1);

namespace EightshiftBoilerplate\AnalyticsGdpr;

use EightshiftLibs\Services\ServiceInterface;

/**
 * Class AnalyticsGdprExample
 */
class AnalyticsGdprExample implements ServiceInterface
{
	/**
	 * Analytics api page slug
	 *
	 * @var string
	 */
	public const ANALYTICS_SLUG = 'analytics';

	/**
	 * Analytics ACF Capability Name.
	 */
	public const ANALYTICS_CAPABILITY = 'edit_others_posts';

	/**
	 * Container ID for Google Tag Manager.
	 *
	 * @var string
	 */
	public const GOOGLE_TAG_MANAGER_CONTAINER_ID = 'google_tag_manager_container_id';

	/**
	 * Pages on which Google Optimize will be activated.
	 *
	 * @var string
	 */
	public const GOOGLE_OPTIMIZE_PAGES = 'google_optimize_pages';

	/**
	 * The intro of the basic modal.
	 *
	 * @var string
	 */
	public const BASIC_MODAL_INTRO = 'basic_modal_intro';

	/**
	 * The basic accept button content.
	 *
	 * @var string
	 */
	public const BASIC_MODAL_ACCEPT_BUTTON = 'basic_modal_accept_button';

	/**
	 * The link for opening the advanced menu.
	 *
	 * @var string
	 */
	public const BASIC_MODAL_OPEN_ADVANCED = 'basic_modal_open_advanced';

	/**
	 * The title of the advanced menu.
	 *
	 * @var string
	 */
	public const ADVANCED_MODAL_TITLE = 'advanced_modal_intro';

	/**
	 * The advanced accept all button content.
	 *
	 * @var string
	 */
	public const ADVANCED_MODAL_ACCEPT_ALL_BUTTON = 'advanced_modal_accept_all_button';

	/**
	 * The advanced reject all button content.
	 *
	 * @var string
	 */
	public const ADVANCED_MODAL_REJECT_ALL_BUTTON = 'advanced_modal_reject_all_button';

	/**
	 * The advanced choice button content.
	 *
	 * @var string
	 */
	public const ADVANCED_MODAL_CHOICE_BUTTON = 'advanced_modal_choice_button';

	/**
	 * Link to the privacy policy page.
	 *
	 * @var string
	 */
	public const ADVANCED_MODAL_PRIVACY_POLICY_LINK = 'advanced_modal_privacy_policy_link';

	/**
	 * Privacy policy publishing date.
	 *
	 * @var string
	 */
	public const PRIVACY_POLICY_PUBLISH_DATE = 'policy_policy_publish_date';

	/**
	 * If required cookies are used.
	 *
	 * @var string
	 */
	public const REQUIRED_COOKIES_ACTIVATED = 'required_cookies_activated';

	/**
	 * The title of the required cookies.
	 *
	 * @var string
	 */
	public const REQUIRED_COOKIES_TITLE = 'required_cookies_title';

	/**
	 * The summary of the required cookies.
	 *
	 * @var string
	 */
	public const REQUIRED_COOKIES_SUMMARY = 'required_cookies_summary';

	/**
	 * If functional cookies are used.
	 *
	 * @var string
	 */
	public const FUNCTIONAL_COOKIES_ACTIVATED = 'functional_cookies_activated';

	/**
	 * The title of the functional cookies.
	 *
	 * @var string
	 */
	public const FUNCTIONAL_COOKIES_TITLE = 'functional_cookies_title';

	/**
	 * The summary of the functional cookies.
	 *
	 * @var string
	 */
	public const FUNCTIONAL_COOKIES_SUMMARY = 'functional_cookies_summary';

	/**
	 * If performance cookies are used.
	 *
	 * @var string
	 */
	public const PERFORMANCE_COOKIES_ACTIVATED = 'performance_cookies_activated';

	/**
	 * The title of the functional cookies.
	 *
	 * @var string
	 */
	public const PERFORMANCE_COOKIES_TITLE = 'performance_cookies_title';

	/**
	 * The summary of the functional cookies.
	 *
	 * @var string
	 */
	public const PERFORMANCE_COOKIES_SUMMARY = 'performance_cookies_summary';

	/**
	 * If marketing cookies are used.
	 *
	 * @var string
	 */
	public const MARKETING_COOKIES_ACTIVATED = 'marketing_cookies_activated';

	/**
	 * The title of the marketing cookies.
	 *
	 * @var string
	 */
	public const MARKETING_COOKIES_TITLE = 'marketing_cookies_title';

	/**
	 * The summary of the marketing cookies.
	 *
	 * @var string
	 */
	public const MARKETING_COOKIES_SUMMARY = 'marketing_cookies_summary';

	/**
	 * The slug used for GDPR modal adjustment.
	 *
	 * @var string
	 */
	public const GDPR_MODAL_SLUG = 'acf-options-gdpr';

	/**
	 * The slug used for GDPR modal adjustment.
	 *
	 * @var string
	 */
	public const GET_GDPR_MODAL_DATA = 'get_gdpr_modal_data';

	/**
	 * Register all the hooks
	 */
	public function register(): void
	{
		\add_action('acf/init', [$this, 'createAnalyticsPage'], 11);
		\add_action('acf/init', [$this, 'registerAnalytics'], 12);

		\add_action('acf/init', [$this, 'createGdprModalPage'], 12);
		\add_action('acf/init', [$this, 'registerGdprModalSettings'], 13);

		\add_filter(static::GET_GDPR_MODAL_DATA, [$this, 'prepareGdprModalData']);
	}

	/**
	 * Create Analytics and GDPR Options page in sidebar
	 *
	 * @return void
	 */
	public function createAnalyticsPage(): void
	{
		if (function_exists('acf_add_options_page')) {
			\acf_add_options_page(
				[
					'page_title' => \esc_html__('Analytics and GDPR settings', 'eightshift-libs'),
					'menu_title' => \esc_html__('Analytics', 'eightshift-libs'),
					'menu_slug'  => static::ANALYTICS_SLUG,
					'capability' => static::ANALYTICS_CAPABILITY,
					'redirect'   => false,
					'icon_url'   => 'dashicons-chart-bar',
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
	public function registerAnalytics(): void
	{
		if (function_exists('acf_add_local_field_group')) {
			\acf_add_local_field_group(
				[
					'key' => 'group_5fc829103758c',
					'title' => \esc_html__('Analytics', 'eightshift-libs'),
					'fields' => [
						[
							'key' => 'field_0927122830152',
							'label' => \esc_html__('Google Optimize on pages', 'eightshift-libs'),
							'name' => static::GOOGLE_OPTIMIZE_PAGES,
							'type' => 'post_object',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id' => '',
							],
							'post_type' => '',
							'taxonomy' => '',
							'allow_null' => 0,
							'multiple' => 1,
							'return_format' => 'id',
							'ui' => 1,
						],
						[
							'key' => 'field_5810891023701',
							'label' => \esc_html__('Google Tag Manager Container ID', 'eightshift-libs'),
							'name' => static::GOOGLE_TAG_MANAGER_CONTAINER_ID,
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => [],
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
					],
					'location' => [
						[
							[
								'param' => 'options_page',
								'operator' => '==',
								'value' => static::ANALYTICS_SLUG,
							],
						],
					],
					'menu_order' => 1,
					'position' => 'normal',
					'style' => 'default',
					'label_placement' => 'top',
					'instruction_placement' => 'label',
					'hide_on_screen' => '',
					'active' => 1,
					'description' => '',
				]
			);
		}
	}

	/**
	 * Create Options page in sidebar.
	 *
	 * @return void
	 */
	public function createGdprModalPage(): void
	{
		if (function_exists('acf_add_options_sub_page') && \current_user_can(self::ANALYTICS_CAPABILITY)) {
			\acf_add_options_sub_page(
				[
					'page_title' => \esc_html__('GDPR Modal', 'eightshift-libs'),
					'menu_title' => \esc_html__('GDPR', 'eightshift-libs'),
					'menu_slug' => self::GDPR_MODAL_SLUG,
					'parent_slug' => self::ANALYTICS_SLUG,
				]
			);
		}
	}

	/**
	 * Populate Options page.
	 *
	 * @return void
	 */
	public function registerGdprModalSettings(): void
	{
		if (function_exists('acf_add_local_field_group')) {
			\acf_add_local_field_group(
				[
					'key' => 'group_5f09139247eb0',
					'title' => \esc_html__('GDPR', 'eightshift-libs'),
					'fields' => [
						[
							'key' => 'field_5f5f29103542a8',
							'label' => \esc_html__('Basic modal open advanced link', 'eightshift-libs'),
							'name' => static::BASIC_MODAL_OPEN_ADVANCED,
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id' => '',
							],
							'default_value' => \esc_html__('Manage cookies', 'eightshift-libs'),
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'maxlength' => '',
						],
						[
							'key' => 'field_5f102929842a8',
							'label' => \esc_html__('Basic modal intro', 'eightshift-libs'),
							'name' => static::BASIC_MODAL_INTRO,
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id' => '',
							],
							// phpcs:ignore Generic.Files.LineLength.TooLong
							'default_value' => \esc_html__('By clicking “I agree”, you accept storing of all cookies on your device to enhance user experience, analyze site usage, and assist in our marketing efforts.', 'eightshift-libs'),
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'maxlength' => '',
						],
						[
							'key' => 'field_5f902a4657483',
							'label' => \esc_html__('Basic modal accept button', 'eightshift-libs'),
							'name' => static::BASIC_MODAL_ACCEPT_BUTTON,
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id' => '',
							],
							'default_value' => \esc_html__('Accept all', 'eightshift-libs'),
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'maxlength' => '',
						],
						[
							'key' => 'field_5f812934842a8',
							'label' => \esc_html__('Advanced modal title', 'eightshift-libs'),
							'name' => static::ADVANCED_MODAL_TITLE,
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id' => '',
							],
							'default_value' => \esc_html__('We need your consent to continue', 'eightshift-libs'),
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'maxlength' => '',
						],
						[
							'key' => 'field_5f473829842a8',
							'label' => \esc_html__('Advanced modal accept all button', 'eightshift-libs'),
							'name' => static::ADVANCED_MODAL_ACCEPT_ALL_BUTTON,
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id' => '',
							],
							'default_value' => \esc_html__('Accept all', 'eightshift-libs'),
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'maxlength' => '',
						],
						[
							'key' => 'field_5f473921022a8',
							'label' => \esc_html__('Advanced modal reject all button', 'eightshift-libs'),
							'name' => static::ADVANCED_MODAL_REJECT_ALL_BUTTON,
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id' => '',
							],
							'default_value' => \esc_html__('Reject all', 'eightshift-libs'),
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'maxlength' => '',
						],
						[
							'key' => 'field_50001229842a8',
							'label' => \esc_html__('Advanced modal confirm choices button', 'eightshift-libs'),
							'name' => static::ADVANCED_MODAL_CHOICE_BUTTON,
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id' => '',
							],
							'default_value' => \esc_html__('Confirm choices', 'eightshift-libs'),
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'maxlength' => '',
						],
						[
							'key' => 'field_61910287b1a5d',
							'label' => \esc_html__('Privacy policy page link', 'eightshift-libs'),
							'name' => static::ADVANCED_MODAL_PRIVACY_POLICY_LINK,
							'type' => 'page_link',
							// phpcs:ignore Generic.Files.LineLength.TooLong
							'instructions' => \esc_html__('Select the page which serves as privacy policy, it will be linked in advanced cookie modal.', 'eightshift-libs'),
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id' => '',
							],
							'post_type' => [
								0 => 'page',
							],
							'taxonomy' => '',
							'allow_null' => 0,
							'allow_archives' => 0,
							'multiple' => 0,
						],
						[
							'key' => 'field_621c12e21e5f8',
							'label' => \esc_html__('Date of publishing privacy policy', 'eightshift-libs'),
							'name' => static::PRIVACY_POLICY_PUBLISH_DATE,
							'type' => 'date_time_picker',
							// phpcs:ignore Generic.Files.LineLength.TooLong
							'instructions' =>  \esc_html__('Date when you\'ve changed the privacy policy. If user accepted the privacy policy before new date is set. GDPR modal will appear to that user.', 'eightshift-libs'),
							'required' => 0,
							'conditional_logic' => 0,
							'default_value' => \gmdate('d m Y H:i:s'),
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id' => '',
							],
							'display_format' => 'F j, Y g:i ZZZ',
							'return_format' => 'F j, Y g:i ZZZ',
							'first_day' => 1,
						],
						[
							'key' => 'field_50009184844a9',
							'label' => \esc_html__('Required Cookies', 'eightshift-libs'),
							'name' => '',
							'type' => 'tab',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id' => '',
							],
							'placement' => 'top',
							'endpoint' => 0,
						],
						[
							'key' => 'field_621cb755cb8e4',
							'label' => \esc_html__('Required Cookies Activated', 'eightshift-libs'),
							'name' => static::REQUIRED_COOKIES_ACTIVATED,
							'type' => 'true_false',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id' => '',
							],
							'message' => \esc_html__('Activate Required cookie level', 'eightshift-libs'),
							'default_value' => 1,
							'ui' => 0,
						],
						[
							'key' => 'field_5f8f7939844ac',
							'label' => \esc_html__('Required Title', 'eightshift-libs'),
							'name' => static::REQUIRED_COOKIES_TITLE,
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id' => '',
							],
							'default_value' => \esc_html__('Required cookies', 'eightshift-libs'),
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'maxlength' => '',
						],
						[
							'key' => 'field_5f5f66482910f',
							'label' => \esc_html__('Required Summary', 'eightshift-libs'),
							'name' => static::REQUIRED_COOKIES_SUMMARY,
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id' => '',
							],
							// phpcs:ignore Generic.Files.LineLength.TooLong
							'default_value' => \esc_html__('Cookies for the basic functionality of the website.', 'eightshift-libs'),
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'maxlength' => '',
						],
						[
							'key' => 'field_f83912e4844a9',
							'label' => \esc_html__('Functional Cookies', 'eightshift-libs'),
							'name' => '',
							'type' => 'tab',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id' => '',
							],
							'placement' => 'top',
							'endpoint' => 0,
						],
						[
							'key' => 'field_621c9182668e4',
							'label' => \esc_html__('Functional Cookies Activated', 'eightshift-libs'),
							'name' => static::FUNCTIONAL_COOKIES_ACTIVATED,
							'type' => 'true_false',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id' => '',
							],
							'message' => \esc_html__('Activate Functional cookie level', 'eightshift-libs'),
							'default_value' => 1,
							'ui' => 0,
						],
						[
							'key' => 'field_5f5f1589164ac',
							'label' => \esc_html__('Functional Title', 'eightshift-libs'),
							'name' => static::FUNCTIONAL_COOKIES_TITLE,
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id' => '',
							],
							'default_value' => \esc_html__('Functional cookies', 'eightshift-libs'),
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'maxlength' => '',
						],
						[
							'key' => 'field_5091024ff452d',
							'label' => \esc_html__('Functional Summary', 'eightshift-libs'),
							'name' => static::FUNCTIONAL_COOKIES_SUMMARY,
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id' => '',
							],
							// phpcs:ignore Generic.Files.LineLength.TooLong
							'default_value' => \esc_html__('Cookies for additional functionality and increased website security.', 'eightshift-libs'),
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'maxlength' => '',
						],
						[
							'key' => 'field_0910234520139',
							'label' => \esc_html__('Performance Cookies', 'eightshift-libs'),
							'name' => '',
							'type' => 'tab',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id' => '',
							],
							'placement' => 'top',
							'endpoint' => 0,
						],
						[
							'key' => 'field_62110242168e4',
							'label' => \esc_html__('Performance Cookies Activated', 'eightshift-libs'),
							'name' => static::PERFORMANCE_COOKIES_ACTIVATED,
							'type' => 'true_false',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id' => '',
							],
							'message' => \esc_html__('Activate Performance cookie level', 'eightshift-libs'),
							'default_value' => 1,
							'ui' => 0,
						],
						[
							'key' => 'field_5f5f1082914ac',
							'label' => \esc_html__('Performance Title', 'eightshift-libs'),
							'name' => static::PERFORMANCE_COOKIES_TITLE,
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id' => '',
							],
							'default_value' => \esc_html__('Performance cookies', 'eightshift-libs'),
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'maxlength' => '',
						],
						[
							'key' => 'field_509191024a52d',
							'label' => \esc_html__('Performance Summary', 'eightshift-libs'),
							'name' => static::PERFORMANCE_COOKIES_SUMMARY,
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id' => '',
							],
							// phpcs:ignore Generic.Files.LineLength.TooLong
							'default_value' => \esc_html__('Performance cookies monitor only the performance of the site as the user interacts with it. These cookies don\'t collect identifiable information on visitor. Data is anonymous.', 'eightshift-libs'),
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'maxlength' => '',
						],
						[
							'key' => 'field_0897712920139',
							'label' => \esc_html__('Marketing Cookies', 'eightshift-libs'),
							'name' => '',
							'type' => 'tab',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id' => '',
							],
							'placement' => 'top',
							'endpoint' => 0,
						],
						[
							'key' => 'field_62110192468e4',
							'label' => \esc_html__('Marketing Cookies Activated', 'eightshift-libs'),
							'name' => static::MARKETING_COOKIES_ACTIVATED,
							'type' => 'true_false',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id' => '',
							],
							'message' => \esc_html__('Activate Marketing cookie level', 'eightshift-libs'),
							'default_value' => 1,
							'ui' => 0,
						],
						[
							'key' => 'field_5f7ff654164ac',
							'label' => \esc_html__('Marketing Title', 'eightshift-libs'),
							'name' => static::MARKETING_COOKIES_TITLE,
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id' => '',
							],
							'default_value' => \esc_html__('Marketing cookies', 'eightshift-libs'),
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'maxlength' => '',
						],
						[
							'key' => 'field_5ff922112684d',
							'label' => \esc_html__('Marketing Summary', 'eightshift-libs'),
							'name' => static::MARKETING_COOKIES_SUMMARY,
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id' => '',
							],
							// phpcs:ignore Generic.Files.LineLength.TooLong
							'default_value' => \esc_html__('Advertising and analytics service cookies that create day-to-day statistics and show ads on their site and on the advertiser’s partners websites.', 'eightshift-libs'),
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'maxlength' => '',
						],
					],
					'location' => [
						[
							[
								'param'    => 'options_page',
								'operator' => '==',
								'value'    => static::GDPR_MODAL_SLUG,
							],
						],
					],
					'menu_order' => 0,
					'position' => 'normal',
					'style' => 'default',
					'label_placement' => 'top',
					'instruction_placement' => 'label',
					'hide_on_screen' => '',
					'active' => 1,
					'description' => '',
				]
			);
		}
	}

	/**
	 * Prepare the data for usage inside of the GDPR modal template.
	 *
	 * @return array<string, array> Prepared array filled with the data from options page.
	 */
	public function prepareGdprModalData(): array
	{
		if (!function_exists('get_field')) {
			return [];
		}

		return [
			'basic' => [
				// phpcs:ignore Generic.Files.LineLength.TooLong
				'intro' => \get_field(static::BASIC_MODAL_INTRO, 'option') ?? \esc_html__('By clicking “I agree”, you accept storing of all cookies on your device to enhance user experience, analyze site usage, and assist in our marketing efforts.', 'eightshift-libs'),
				'acceptButton' => \get_field(static::BASIC_MODAL_ACCEPT_BUTTON, 'option') ?? \esc_html__('Accept all', 'eightshift-libs'),
				'openAdvanced' => \get_field(static::BASIC_MODAL_OPEN_ADVANCED, 'option') ?? \esc_html__('Manage cookies', 'eightshift-libs'),
			],
			'advanced' => [
				'title' => \get_field(static::ADVANCED_MODAL_TITLE, 'option') ?? \esc_html__('We need your consent to continue', 'eightshift-libs'),
				'acceptAllButton' => \get_field(static::ADVANCED_MODAL_ACCEPT_ALL_BUTTON, 'option') ?? \esc_html__('Accept all', 'eightshift-libs'),
				'rejectAllButton' => \get_field(static::ADVANCED_MODAL_REJECT_ALL_BUTTON, 'option') ??  \esc_html__('Reject all', 'eightshift-libs'),
				'choiceButton' => \get_field(static::ADVANCED_MODAL_CHOICE_BUTTON, 'option') ?? \esc_html__('Confirm choices', 'eightshift-libs'),
				'privacyPolicyLink' => \get_field(static::ADVANCED_MODAL_PRIVACY_POLICY_LINK, 'option') ?? '',
			],
			'publishDate' => \get_field(static::PRIVACY_POLICY_PUBLISH_DATE, 'option') ?? \gmdate('F j, Y g:i ZZZ'),
			'levels' => [
				'required' => [
					'title' => \get_field(static::REQUIRED_COOKIES_TITLE, 'option') ?? \esc_html__('Required cookies', 'eightshift-libs'),
					'summary' => \get_field(static::REQUIRED_COOKIES_SUMMARY, 'option') ?? \esc_html__('Cookies for the basic functionality of the website.', 'eightshift-libs'),
					'activated' => \get_field(static::REQUIRED_COOKIES_ACTIVATED, 'option') ?? true,
				],
				'functional' => [
					'title' => \get_field(static::FUNCTIONAL_COOKIES_TITLE, 'option') ?? \esc_html__('Functional cookies', 'eightshift-libs'),
					// phpcs:ignore Generic.Files.LineLength.TooLong
					'summary' => \get_field(static::FUNCTIONAL_COOKIES_SUMMARY, 'option') ?? \esc_html__('Cookies for additional functionality and increased website security.', 'eightshift-libs'),
					'activated' => \get_field(static::FUNCTIONAL_COOKIES_ACTIVATED, 'option') ?? true,
				],
				'performance' => [
					'title' => \get_field(static::PERFORMANCE_COOKIES_TITLE, 'option') ?? \esc_html__('Performance cookies', 'eightshift-libs'),
					// phpcs:ignore Generic.Files.LineLength.TooLong
					'summary' => \get_field(static::PERFORMANCE_COOKIES_SUMMARY, 'option') ?? \esc_html__('Performance cookies monitor only the performance of the site as the user interacts with it. These cookies don\'t collect identifiable information on visitor. Data is anonymous.', 'eightshift-libs'),
					'activated' => \get_field(static::PERFORMANCE_COOKIES_ACTIVATED, 'option') ?? true,
				],
				'marketing' => [
					'title' => \get_field(static::MARKETING_COOKIES_TITLE, 'option') ?? \esc_html__('Marketing cookies', 'eightshift-libs'),
					// phpcs:ignore Generic.Files.LineLength.TooLong
					'summary' => \get_field(static::MARKETING_COOKIES_SUMMARY, 'option') ?? \esc_html__('Advertising and analytics service cookies that create day-to-day statistics and show ads on their site and on the advertiser’s partners websites.', 'eightshift-libs'),
					'activated' => \get_field(static::REQUIRED_COOKIES_ACTIVATED, 'option') ?? true,
				],
			],
		];
	}
}
