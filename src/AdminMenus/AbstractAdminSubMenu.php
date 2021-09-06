<?php

/**
 * File that holds base abstract class for admin sub menus generation.
 *
 * @package EightshiftLibs\AdminMenus
 */

declare(strict_types=1);

namespace EightshiftLibs\AdminMenus;

/**
 * Abstract class AbstractAdminSubMenu class.
 *
 * Class responsible for creating admin sub menus.
 */
abstract class AbstractAdminSubMenu extends AbstractAdminMenu
{

	/**
	 * Register all the hooks
	 *
	 * @return void
	 */
	public function register(): void
	{
		\add_action(
			'admin_menu',
			function () {
				\add_submenu_page(
					$this->getParentMenu(),
					$this->getTitle(),
					$this->getMenuTitle(),
					$this->getCapability(),
					$this->getMenuSlug(),
					[$this, 'processAdminSubmenu']
				);
			}
		);
	}

	/**
	 * Process the admin submenu attributes and prepare rendering.
	 *
	 * The echo doesn't need to be escaped since it's escaped
	 * in the render method.
	 *
	 * @param array<string, mixed>|string $attr Attributes as passed to the admin menu.
	 *
	 * @return void The rendered content needs to be echoed.
	 * @throws \Exception Exception in case the component is missing.
	 */
	public function processAdminSubmenu($attr): void
	{
		$attr = $this->processAttributes($attr);

		$attr['adminMenuSlug'] = $this->getMenuSlug();
		$attr['nonceField'] = $this->renderNonce();

		echo $this->render((array)$attr); // phpcs:ignore
	}

	/**
	 * Get the slug of the parent menu.
	 *
	 * @return string The slug name for the parent menu (or the file name of a standard WordPress admin page.
	 */
	abstract protected function getParentMenu(): string;
}
