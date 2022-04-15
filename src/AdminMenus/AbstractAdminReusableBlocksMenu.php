<?php

/**
 * File that holds base abstract class for reusable blocks admin menu generation.
 *
 * @package EightshiftLibs\AdminMenus
 */

declare(strict_types=1);

namespace EightshiftLibs\AdminMenus;

/**
 * Abstract class AbstractAdminReusableBlocksMenu class.
 *
 * Class responsible for creating reusable blocks admin menu.
 */
abstract class AbstractAdminReusableBlocksMenu extends AbstractAdminMenu
{
	/**
	 * Register all the hooks.
	 *
	 * @return void
	 */
	public function register(): void
	{
		\add_action(
			'admin_menu',
			function () {
				\add_menu_page(
					$this->getTitle(),
					$this->getMenuTitle(),
					$this->getCapability(),
					$this->getMenuSlug(),
					'', // @phpstan-ignore-line
					$this->getIcon(),
					$this->getPosition()
				);
			}
		);
	}

	/**
	 * Get the view component that will render correct view.
	 *
	 * @return string View uri.
	 */
	protected function getViewComponent(): string
	{
		return '';
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
}
