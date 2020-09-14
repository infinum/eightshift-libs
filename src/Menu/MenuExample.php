<?php

/**
 * The Menu specific functionality.
 *
 * @package EightshiftLibs\Menu
 */

declare(strict_types=1);

namespace EightshiftLibs\Menu;

use EightshiftLibs\Menu\AbstractMenu;

/**
 * Class MenuExample
 */
class MenuExample extends AbstractMenu
{

	/**
	 * Register all the hooks
	 *
	 * @return void
	 */
	public function register(): void
	{
		\add_action('after_setup_theme', [ $this, 'registerMenuPositions' ], 11);
	}

	/**
	 * Return all menu poistions
	 *
	 * @return array Menu positions with slug => name structure.
	 */
	public function getMenuPositions(): array
	{
		return [
			'header_main_nav' => \esc_html__('Main Menu', 'eightshift-libs'),
		];
	}
}
