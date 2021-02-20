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
	 * Main menu position identifier.
	 *
	 * Used as a placeholder, can be overwritten.
	 *
	 * @var string
	 */
	public const MAIN_MENU = 'header_main_nav';

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
	 * @return array Of menu positions with name and slug.
	 */
	public function getMenuPositions(): array
	{
		return [];
	}

	/**
	 * This method returns an instance of the bemMenuWalker class with the following arguments
	 *
	 * @param string $name Name of the menu. If theme location is provided and set
	 *                     it will render the menu from the theme location.
	 *                     If not it will render the name of the menu.
	 * @param string $cssClassPrefix This string will prefix all of the menu's classes, BEM syntax friendly.
	 * @param string $parentClass This string will add class to only top level list element.
	 * @param string $jsModifier This string will add class to only top level list element.
	 * @param string $cssClassModifiers Provide either a string or array of values to apply extra classes
	 *                                   to the <ul> but not the <li's>.
	 * @param bool   $echo Echo the menu.
	 *
	 * @return string|false|void Menu output if $echo is false, false if there are no items or no menu was found.
	 */
	public static function bemMenu(
		string $name = self::MAIN_MENU,
		string $cssClassPrefix = 'main-menu',
		string $parentClass = '',
		string $jsModifier = '',
		$cssClassModifiers = '',
		bool $echo = true
	) {
		// Check to see if any css modifiers were supplied.
		$modifiers = '';

		if (!empty($cssClassModifiers)) {
			if (is_array($cssClassModifiers)) {
				$modifiers = implode(' ', $cssClassModifiers);
			} elseif (is_string($cssClassModifiers)) {
				$modifiers = $cssClassModifiers;
			}
		}

		$menuWrapperClass = trim($cssClassPrefix . ' ' . $modifiers);

		$args = [
			'container' => false,
			'items_wrap' => '<ul class="' . trim($menuWrapperClass . ' ' . $jsModifier) . '">%3$s</ul>',
			'echo' => $echo,
			'walker' => new BemMenuWalker($cssClassPrefix, $parentClass),
		];

		if (\has_nav_menu($name)) {
			$args['theme_location'] = $name;
		} else {
			$args['menu'] = $name;
		}

		return \wp_nav_menu($args);
	}
}
