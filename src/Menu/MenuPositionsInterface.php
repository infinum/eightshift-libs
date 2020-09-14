<?php

/**
 * Projects MenuPositionsInterface interface.
 *
 * Used to define available menu positions in your project.
 *
 * @package EightshiftLibs\Menu
 */

declare(strict_types=1);

namespace EightshiftLibs\Menu;

/**
 * Interface MenuPositionsInterface
 */
interface MenuPositionsInterface
{

	/**
	 * Return all menu poistions
	 *
	 * @return array Of menu positions with name and slug.
	 */
	public function getMenuPositions(): array;
}
