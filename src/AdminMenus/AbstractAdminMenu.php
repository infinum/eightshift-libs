<?php

/**
 * File that holds base abstract class for admin menus generation.
 *
 * @package EightshiftLibs\AdminMenus
 */

declare(strict_types=1);

namespace EightshiftLibs\AdminMenus;

use EightshiftLibs\Services\ServiceInterface;
use Exception;

/**
 * Abstract class AbstractAdminMenu class.
 *
 * Class responsible for creating admin menus, separately from CPT admin menus.
 */
abstract class AbstractAdminMenu implements ServiceInterface
{
	/**
	 * Register all the hooks
	 *
	 * @return void
	 */
	public function register(): void
	{
		\add_action('admin_menu', [$this, 'callback'], $this->getPriorityOrder());
	}

	/**
	 * Return action callback method.
	 *
	 * @return void
	 */
	public function callback(): void
	{
		\add_menu_page(
			$this->getTitle(),
			$this->getMenuTitle(),
			$this->getCapability(),
			$this->getMenuSlug(),
			[$this, 'processAdminMenu'],
			$this->getIcon(),
			$this->getPosition()
		);
	}

	/**
	 * Return hook priority order.
	 *
	 * @return integer
	 */
	public function getPriorityOrder(): int
	{
		return 10;
	}

	/**
	 * Process the admin menu attributes and prepare rendering.
	 *
	 * The echo doesn't need to be escaped since it's escaped
	 * in the render method.
	 *
	 * @param array<string, mixed>|string $attr Attributes as passed to the admin menu.
	 *
	 * @return void The rendered content needs to be echoed.
	 * @throws Exception Exception in case the component is missing.
	 */
	public function processAdminMenu($attr): void
	{
		$attr = $this->processAttributes($attr);

		$attr['adminMenuSlug'] = $this->getMenuSlug();
		$attr['nonceField'] = $this->renderNonce();

		echo $this->getViewComponent($attr); // phpcs:ignore
	}

	/**
	 * Get the title to use for the admin page.
	 *
	 * @return string The text to be displayed in the title tags of the page when the menu is selected.
	 */
	abstract protected function getTitle(): string;

	/**
	 * Get the menu title to use for the admin menu.
	 *
	 * @return string The text to be used for the menu.
	 */
	abstract protected function getMenuTitle(): string;

	/**
	 * Get the capability required for this menu to be displayed.
	 *
	 * @return string The capability required for this menu to be displayed to the user.
	 */
	abstract protected function getCapability(): string;

	/**
	 * Get the menu slug.
	 *
	 * @return string The slug name to refer to this menu by.
	 *                Should be unique for this menu page and only include lowercase alphanumeric,
	 *                dashes, and underscores characters to be compatible with sanitize_key().
	 */
	abstract protected function getMenuSlug(): string;

	/**
	 * Get the view component that will render correct view.
	 *
	 * @param array<string, mixed> $attributes Attributes passed to the view.
	 *
	 * @return string View uri.
	 */
	abstract protected function getViewComponent(array $attributes): string;

	/**
	 * Process the admin menu attributes.
	 *
	 * @param array<string, mixed>|string $attr Raw admin menu attributes passed into the
	 *                           admin menu function.
	 *
	 * @return array<string, mixed> Processed admin menu attributes.
	 */
	abstract protected function processAttributes($attr): array;

	/**
	 * Render the nonce.
	 *
	 * @return string|false Hidden field with a nonce.
	 */
	protected function renderNonce()
	{
		\ob_start();

		\wp_nonce_field(
			$this->getNonceAction(),
			$this->getNonceName()
		);

		return \ob_get_clean();
	}

	/**
	 * Get the action of the nonce to use.
	 *
	 * @return string Action of the nonce.
	 */
	protected function getNonceAction(): string
	{
		return "{$this->getMenuSlug()}_action";
	}

	/**
	 * Get the name of the nonce to use.
	 *
	 * @return string Name of the nonce.
	 */
	protected function getNonceName(): string
	{
		return "{$this->getMenuSlug()}_nonce";
	}

	/**
	 * Get the URL to the icon to be used for this menu
	 *
	 * @return string The URL to the icon to be used for this menu.
	 *                * Pass a base64-encoded SVG using a data URI, which will be colored to match
	 *                  the color scheme. This should begin with 'data:image/svg+xml;base64,'.
	 *                * Pass the name of a Dashicons helper class to use a font icon,
	 *                  e.g. 'dashicons-chart-pie'.
	 *                * Pass 'none' to leave div.wp-menu-image empty so an icon can be added via CSS.
	 */
	protected function getIcon(): string
	{
		return 'none';
	}

	/**
	 * Get the position of the menu.
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
		return 100;
	}
}
