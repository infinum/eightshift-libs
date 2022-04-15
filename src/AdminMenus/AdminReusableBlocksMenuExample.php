<?php

/**
 * File that holds class for admin menu example.
 *
 * @package EightshiftBoilerplate\AdminMenus
 */

declare(strict_types=1);

namespace EightshiftBoilerplate\AdminMenus;

use EightshiftLibs\AdminMenus\AbstractAdminReusableBlocksMenu;

/**
 * AdminReusableBlocksMenuExample class.
 */
class AdminReusableBlocksMenuExample extends AbstractAdminReusableBlocksMenu
{
	/**
	 * Reusable blocks Capability.
	 */
	public const ADMIN_REUSABLE_BLOCKS_MENU_CAPABILITY = 'edit_posts';

	/**
	 * Menu slug for reusable blocks menu.
	 *
	 * @var string
	 */
	public const ADMIN_REUSABLE_BLOCKS_MENU_SLUG = 'edit.php?post_type=wp_block';

	/**
	 * Menu icon for reusable blocks menu.
	 *
	 * @var string
	 */
	public const ADMIN_REUSABLE_BLOCKS_MENU_ICON = 'dashicons-editor-table';

	/**
	 * Menu position for reusable blocks menu.
	 *
	 * @var int
	 */
	public const ADMIN_REUSABLE_BLOCKS_MENU_POSITION = 4;

	/**
	 * Get the title to use for the admin page.
	 *
	 * @return string The text to be displayed in the title tags of the page when the menu is selected.
	 */
	protected function getTitle(): string
	{
		return \esc_html__('Reusable Blocks', 'eightshift-libs');
	}

	/**
	 * Get the menu title to use for the admin menu.
	 *
	 * @return string The text to be used for the menu.
	 */
	protected function getMenuTitle(): string
	{
		return \esc_html__('Reusable Blocks', 'eightshift-libs');
	}

	/**
	 * Get the capability required for reusable block menu to be displayed.
	 *
	 * @return string The capability required for reusable block menu to be displayed to the user.
	 */
	protected function getCapability(): string
	{
		return self::ADMIN_REUSABLE_BLOCKS_MENU_CAPABILITY;
	}

	/**
	 * Get the menu slug.
	 *
	 * @return string The slug name to refer to reusable block menu by.
	 *                Should be unique for reusable block menu page and only include lowercase alphanumeric,
	 *                dashes, and underscores characters to be compatible with sanitize_key().
	 */
	protected function getMenuSlug(): string
	{
		return self::ADMIN_REUSABLE_BLOCKS_MENU_SLUG;
	}

	/**
	 * Get the URL to the icon to be used for reusable block menu.
	 *
	 * @return string The URL to the icon to be used for reusable block menu.
	 *                * Pass a base64-encoded SVG using a data URI, which will be colored to match
	 *                  the color scheme. This should begin with 'data:image/svg+xml;base64,'.
	 *                * Pass the name of a Dashicons helper class to use a font icon,
	 *                  e.g. 'dashicons-chart-pie'.
	 *                * Pass 'none' to leave div.wp-menu-image empty so an icon can be added via CSS.
	 */
	protected function getIcon(): string
	{
		return self::ADMIN_REUSABLE_BLOCKS_MENU_ICON;
	}

	/**
	 * Get the position of the reusable blocks menu.
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
		return self::ADMIN_REUSABLE_BLOCKS_MENU_POSITION;
	}
}
