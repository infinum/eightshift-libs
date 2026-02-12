<?php

/**
 * Tests for AbstractAdminSubMenu class
 *
 * @package EightshiftLibs\Tests\Unit\AdminMenus
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\AdminMenus;

use Brain\Monkey;
use Brain\Monkey\Functions;
use EightshiftLibs\AdminMenus\AbstractAdminSubMenu;
use EightshiftLibs\Services\ServiceInterface;
use EightshiftLibs\Tests\BaseTestCase;

/**
 * AbstractAdminSubMenuTest class
 */
class AbstractAdminSubMenuTest extends BaseTestCase
{
	/**
	 * Set up before each test
	 *
	 * @return void
	 */
	protected function setUp(): void
	{
		parent::setUp();
		Monkey\setUp();
	}

	/**
	 * Tear down after each test
	 *
	 * @return void
	 */
	protected function tearDown(): void
	{
		Monkey\tearDown();
		parent::tearDown();
	}

	/**
	 * Test that AbstractAdminSubMenu implements ServiceInterface
	 *
	 * @return void
	 */
	public function testImplementsServiceInterface(): void
	{
		$submenu = new ConcreteAdminSubMenu();

		$this->assertInstanceOf(ServiceInterface::class, $submenu);
	}

	/**
	 * Test that register method is callable
	 *
	 * @return void
	 */
	public function testRegisterIsCallable(): void
	{
		$submenu = new ConcreteAdminSubMenu();

		$this->assertTrue(\is_callable([$submenu, 'register']));
	}

	/**
	 * Test that getPriorityOrder returns 200
	 *
	 * @return void
	 */
	public function testGetPriorityOrderReturns200(): void
	{
		$submenu = new ConcreteAdminSubMenu();

		$this->assertEquals(200, $submenu->getPriorityOrder());
	}

	/**
	 * Test that getParentMenu returns expected value
	 *
	 * @return void
	 */
	public function testGetParentMenuReturnsExpectedValue(): void
	{
		$submenu = new ConcreteAdminSubMenu();

		$reflection = new \ReflectionMethod($submenu, 'getParentMenu');

		$this->assertEquals('options-general.php', $reflection->invoke($submenu));
	}

	/**
	 * Test that register method adds action hook
	 *
	 * @return void
	 */
	public function testRegisterAddsAdminMenuAction(): void
	{
		Functions\expect('add_action')
			->once()
			->with('admin_menu', \Mockery::type('array'), 200);

		$submenu = new ConcreteAdminSubMenu();
		$submenu->register();
	}

	/**
	 * Test that callback calls add_submenu_page with correct arguments
	 *
	 * @return void
	 */
	public function testCallbackCallsAddSubmenuPage(): void
	{
		Functions\expect('add_submenu_page')
			->once()
			->with(
				'options-general.php',
				'Test Admin SubMenu',
				'Test SubMenu',
				'manage_options',
				'test-submenu',
				\Mockery::type('array')
			);

		$submenu = new ConcreteAdminSubMenu();
		$submenu->callback();
	}

	/**
	 * Test that processAdminSubmenu calls getViewComponent with correct attributes
	 *
	 * @return void
	 */
	public function testProcessAdminSubmenuSetsAttributesAndRendersView(): void
	{
		Functions\when('ob_start')->justReturn();
		Functions\when('wp_nonce_field')->justReturn();
		Functions\when('ob_get_clean')->justReturn('nonce_field_html');

		$submenu = new ConcreteAdminSubMenu();

		$this->expectOutputString('');
		$submenu->processAdminSubmenu([]);
	}
}

/**
 * Concrete implementation of AbstractAdminSubMenu for testing
 */
class ConcreteAdminSubMenu extends AbstractAdminSubMenu
{
	/**
	 * Get the parent menu
	 *
	 * @return string
	 */
	protected function getParentMenu(): string
	{
		return 'options-general.php';
	}

	/**
	 * Get the title
	 *
	 * @return string
	 */
	protected function getTitle(): string
	{
		return 'Test Admin SubMenu';
	}

	/**
	 * Get the menu title
	 *
	 * @return string
	 */
	protected function getMenuTitle(): string
	{
		return 'Test SubMenu';
	}

	/**
	 * Get the capability
	 *
	 * @return string
	 */
	protected function getCapability(): string
	{
		return 'manage_options';
	}

	/**
	 * Get the menu slug
	 *
	 * @return string
	 */
	protected function getMenuSlug(): string
	{
		return 'test-submenu';
	}

	/**
	 * Process attributes
	 *
	 * @param array<string, mixed>|string $attr Attributes.
	 *
	 * @return array<string, mixed>
	 */
	protected function processAttributes($attr): array
	{
		return [];
	}

	/**
	 * Get view component
	 *
	 * @param array<string, mixed> $attr Attributes.
	 *
	 * @return string
	 */
	protected function getViewComponent(array $attr): string
	{
		return '';
	}
}
