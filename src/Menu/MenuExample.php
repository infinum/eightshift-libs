<?php

/**
 * The Menu specific functionality.
 *
 * @package EightshiftBoilerplate\Menu
 */

declare(strict_types=1);

namespace EightshiftBoilerplate\Menu;

use EightshiftLibs\Menu\AbstractMenu;

/**
 * Class MenuExample
 */
class MenuExample extends AbstractMenu
{
	/**
	 * Main menu position identifier.
	 *
	 * @var string
	 */
	public const MAIN_MENU = 'header_main_nav';

	/**
	 * Register all the hooks
	 *
	 * @return void
	 */
	public function register(): void
	{
		\add_action('after_setup_theme', [$this, 'registerMenuPositions'], 11);
	}

	/**
	 * Return all menu positions
	 *
	 * @return array<string> Menu positions with slug => name structure.
	 */
	public function getMenuPositions(): array
	{
		return [
			self::MAIN_MENU => \esc_html__('Main Menu', 'eightshift-libs'),
		];
	}
}
