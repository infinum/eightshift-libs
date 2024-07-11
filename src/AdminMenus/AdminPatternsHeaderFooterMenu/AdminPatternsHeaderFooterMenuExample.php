<?php

/**
 * File that holds class for patterns header/footer example.
 *
 * @package %g_namespace%\AdminMenus
 */

declare(strict_types=1);

namespace %g_namespace%\AdminMenus;

use %g_use_libs%\AdminMenus\AbstractAdminMenu;
use %g_use_libs%\Helpers\Helpers;
use WP_Query;

/**
 * PatternsHeaderFooterExample class.
 */
class PatternsHeaderFooterExample extends AbstractAdminMenu
{
	/**
	 * Patterns Capability.
	 */
	public const ADMIN_MENU_CAPABILITY = '%capability%';

	/**
	 * Menu slug for patterns menu.
	 *
	 * @var string
	 */
	public const ADMIN_MENU_SLUG = 'es-header-footer';

	/**
	 * Admin icon.
	 */
	public const ADMIN_ICON = 'dashicons-embed-photo';

	/**
	 * Menu position for patterns menu.
	 *
	 * @var int
	 */
	public const ADMIN_MENU_POSITION = 59;

	/**
	 * Settings section name (for WP Settings API).
	 *
	 * @var string
	 */
	public const SETTINGS_SECTION_NAME = 'es-header-footer-section';

	/**
	 * Slug for the WP messages in admin.
	 *
	 * @var string
	 */
	public const ADMIN_MESSAGE_SLUG = 'es-header-footer-messages';

	/**
	 * "Header partial" option name.
	 *
	 * @var string
	 */
	public const HEADER_PARTIAL = 'es-header-partial';

	/**
	 * "Footer partial" option name.
	 *
	 * @var string
	 */
	public const FOOTER_PARTIAL = 'es-footer-partial';

	/**
	 * Register all the hooks.
	 *
	 * @return void
	 */
	public function register(): void
	{
		\add_action('admin_init', [$this, 'registerWpSettings']);
		\add_action('admin_menu', [$this, 'callback'], $this->getPriorityOrder());
	}

	/**
	 * Get the title to use for the admin page.
	 *
	 * @return string The text to be displayed in the title tags of the page when the menu is selected.
	 */
	protected function getTitle(): string
	{
		return \esc_html__('%title%', '%g_textdomain%');
	}

	/**
	 * Get the menu title to use for the admin menu.
	 *
	 * @return string The text to be used for the menu.
	 */
	protected function getMenuTitle(): string
	{
		return \esc_html__('%menu_title%', '%g_textdomain%');
	}

	/**
	 * Get the capability required for patterns menu to be displayed.
	 *
	 * @return string The capability required for patterns menu to be displayed to the user.
	 */
	protected function getCapability(): string
	{
		return self::ADMIN_MENU_CAPABILITY;
	}

	/**
	 * Get the menu slug.
	 *
	 * @return string The slug name to refer to patterns menu by.
	 *                Should be unique for patterns menu page and only include lowercase alphanumeric,
	 *                dashes, and underscores characters to be compatible with sanitize_key().
	 */
	protected function getMenuSlug(): string
	{
		return self::ADMIN_MENU_SLUG;
	}

	/**
	 * Get the URL to the icon to be used for patterns menu.
	 *
	 * @return string The URL to the icon to be used for patterns menu.
	 *                * Pass a base64-encoded SVG using a data URI, which will be colored to match
	 *                  the color scheme. This should begin with 'data:image/svg+xml;base64,'.
	 *                * Pass the name of a Dashicons helper class to use a font icon,
	 *                  e.g. 'dashicons-chart-pie'.
	 *                * Pass 'none' to leave div.wp-menu-image empty so an icon can be added via CSS.
	 */
	protected function getIcon(): string
	{
		return self::ADMIN_ICON;
	}

	/**
	 * Get the position of the patterns menu.
	 *
	 * @return int Number that indicates the position of the menu.
	 * 5   - below Posts
	 * 10  - below Media
	 * 15  - below Links
	 * 20  - below Pages
	 * 25  - below comments
	 * 60  - below first separator
	 * 65  - below Plugins
	 * 70  - below Users
	 * 75  - below Tools
	 * 80  - below Settings
	 * 100 - below second separator
	 */
	protected function getPosition(): int
	{
		return self::ADMIN_MENU_POSITION;
	}

	/**
	 * Get the view component that will render correct view.
	 *
	 * @return string View URI.
	 */
	protected function getViewComponent(): string
	{
		return 'admin-header-footer-picker';
	}

	/**
	 * Process the admin menu attributes.
	 *
	 * Here you can get any kind of metadata, query the database, etc..
	 * This data will be passed to the component view to be rendered out in the
	 * processAdminMenu parent method.
	 *
	 * @param array<string, mixed>|string $attr Raw admin menu attributes passed into the
	 *                           admin menu function.
	 *
	 * @return array<string, mixed> Processed admin menu attributes.
	 */
	protected function processAttributes($attr): array
	{
		return [];
	}

	// Supporting functions for defining settings and sections.

	/**
	 * Register settings for use with WP Settings API.
	 *
	 * @return void
	 */
	public function registerWpSettings(): void
	{
		\register_setting(self::ADMIN_MENU_SLUG, self::HEADER_PARTIAL);
		\register_setting(self::ADMIN_MENU_SLUG, self::FOOTER_PARTIAL);

		\add_settings_section(self::SETTINGS_SECTION_NAME, '', fn () => '', self::ADMIN_MENU_SLUG);

		\add_settings_field(
			self::HEADER_PARTIAL,
			\__('Header partial', '%g_textdomain%'),
			[$this, 'renderPartialSelector'],
			self::ADMIN_MENU_SLUG,
			self::SETTINGS_SECTION_NAME,
			[
				'label_for' => self::HEADER_PARTIAL,
				'type' => 'header',
			]
		);

		\add_settings_field(
			self::FOOTER_PARTIAL,
			\__('Footer partial', '%g_textdomain%'),
			[$this, 'renderPartialSelector'],
			self::ADMIN_MENU_SLUG,
			self::SETTINGS_SECTION_NAME,
			[
				'label_for' => self::FOOTER_PARTIAL,
				'type' => 'footer',
			]
		);
	}

	/**
	 * Renders the "Header partial" select menu.
	 *
	 * @param array<mixed> $args Arguments to pass.
	 * @return void
	 */
	public function renderPartialSelector($args): void
	{
		$type = isset($args['type']) && $args['type'] === 'header' ? self::HEADER_PARTIAL : self::FOOTER_PARTIAL;

		$reusableBlocksQuery = new WP_Query([
			'post_type' => 'wp_block',
			'posts_per_page' => 1000, // phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_posts_per_page
			'post_status' => 'publish',
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		]);

		if ($reusableBlocksQuery->have_posts()) {
			$currentValue = \get_option($type);
			?>
			<select id="<?php echo \esc_attr($args['label_for']); ?>" name="<?php echo \esc_attr($type); ?>">
				<option value="">
					&mdash; <?php \esc_html_e('None', '%g_textdomain%'); ?> &mdash;
				</option>

				<?php
				while ($reusableBlocksQuery->have_posts()) {
					$reusableBlocksQuery->the_post();
					$postId = (string) \get_the_ID();
					$postTitle = \get_the_title();
					?>
					<option value="<?php echo \esc_attr($postId); ?>"
						<?php \selected($currentValue, $postId, true); ?>
					>
						<?php echo \esc_html($postTitle); ?>
					</option>
					<?php
				}

				\wp_reset_postdata();
				?>
			</select>
		<?php } else { ?>
			<i><?php echo \esc_html__('No patterns found.', '%g_textdomain%'); ?></i>
			<?php
		}
	}

	/**
	 * Renders a patterns partial.
	 *
	 * @param int|string $partialId Block partial ID.
	 * @return void
	 */
	public static function renderPartial($partialId): void
	{
		if (empty((string) $partialId)) {
			return;
		}

		$blocksToRender = \parse_blocks(\get_the_content(null, false, $partialId));

		// Filter out empty blocks.
		$blocksToRenderFiltered = \array_values(
			\array_filter(
				$blocksToRender,
				static function ($blockArray) {
					return !empty($blockArray['blockName']);
				}
			)
		);

		$blocksToRenderRendered = \array_map(
			static function ($block) {
				return \render_block($block);
			},
			$blocksToRenderFiltered // phpcs:ignore
		);

		echo Helpers::ensureString($blocksToRenderRendered); // phpcs:ignore
	}
}
