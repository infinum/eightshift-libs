<?php

/**
 * The Menu specific functionality.
 *
 * @package EightshiftLibs\Menu
 */

declare(strict_types=1);

namespace EightshiftLibs\Menu;

use EightshiftLibs\Services\ServiceInterface;

/**
 * Class Menu
 */
abstract class AbstractMenu implements ServiceInterface, MenuPositionsInterface
{
	/**
	 * Register All Menu positions
	 *
	 * @return void
	 */
	public function registerMenuPositions()
	{
		\register_nav_menus(
			$this->getMenuPositions()
		);
	}

	/**
	 * Return all menu positions
	 *
	 * @return array<string, mixed> Of menu positions with name and slug.
	 */
	public function getMenuPositions(): array
	{
		return [];
	}
}
