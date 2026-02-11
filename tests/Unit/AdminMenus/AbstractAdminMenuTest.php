<?php

/**
 * Tests for AbstractAdminMenu class
 *
 * @package EightshiftLibs\Tests\Unit\AdminMenus
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\AdminMenus;

use Brain\Monkey;
use Brain\Monkey\Functions;
use EightshiftLibs\AdminMenus\AbstractAdminMenu;
use EightshiftLibs\Services\ServiceInterface;
use EightshiftLibs\Tests\BaseTestCase;

/**
 * AbstractAdminMenuTest class
 */
class AbstractAdminMenuTest extends BaseTestCase
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
	 * Test that AbstractAdminMenu implements ServiceInterface
	 *
	 * @return void
	 */
	public function testImplementsServiceInterface(): void
	{
		$menu = new ConcreteAdminMenu();

		$this->assertInstanceOf(ServiceInterface::class, $menu);
	}

	/**
	 * Test that register method is callable
	 *
	 * @return void
	 */
	public function testRegisterIsCallable(): void
	{
		$menu = new ConcreteAdminMenu();

		$this->assertTrue(\is_callable([$menu, 'register']));
	}

	/**
	 * Test that getPriorityOrder returns 10
	 *
	 * @return void
	 */
	public function testGetPriorityOrderReturns10(): void
	{
		$menu = new ConcreteAdminMenu();

		$this->assertEquals(10, $menu->getPriorityOrder());
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
			->with('admin_menu', \Mockery::type('array'), 10);

		$menu = new ConcreteAdminMenu();
		$menu->register();
	}

	/**
	 * Test that callback calls add_menu_page with correct arguments
	 *
	 * @return void
	 */
	public function testCallbackCallsAddMenuPage(): void
	{
		Functions\expect('add_menu_page')
			->once()
			->with(
				'Test Admin Menu',
				'Test Menu',
				'manage_options',
				'test-menu',
				\Mockery::type('array'),
				'none',
				100
			);

		$menu = new ConcreteAdminMenu();
		$menu->callback();
	}

	/**
	 * Test that getIcon returns default 'none'
	 *
	 * @return void
	 */
	public function testGetIconReturnsNone(): void
	{
		$menu = new ConcreteAdminMenu();

		$reflection = new \ReflectionMethod($menu, 'getIcon');

		$this->assertEquals('none', $reflection->invoke($menu));
	}

	/**
	 * Test that getPosition returns default 100
	 *
	 * @return void
	 */
	public function testGetPositionReturns100(): void
	{
		$menu = new ConcreteAdminMenu();

		$reflection = new \ReflectionMethod($menu, 'getPosition');

		$this->assertEquals(100, $reflection->invoke($menu));
	}

	/**
	 * Test that getNonceAction returns correct format
	 *
	 * @return void
	 */
	public function testGetNonceActionReturnsCorrectFormat(): void
	{
		$menu = new ConcreteAdminMenu();

		$reflection = new \ReflectionMethod($menu, 'getNonceAction');

		$this->assertEquals('test-menu_action', $reflection->invoke($menu));
	}

	/**
	 * Test that getNonceName returns correct format
	 *
	 * @return void
	 */
	public function testGetNonceNameReturnsCorrectFormat(): void
	{
		$menu = new ConcreteAdminMenu();

		$reflection = new \ReflectionMethod($menu, 'getNonceName');

		$this->assertEquals('test-menu_nonce', $reflection->invoke($menu));
	}

	/**
	 * Test that getTitle returns expected value
	 *
	 * @return void
	 */
	public function testGetTitleReturnsExpectedValue(): void
	{
		$menu = new ConcreteAdminMenu();

		$reflection = new \ReflectionMethod($menu, 'getTitle');

		$this->assertEquals('Test Admin Menu', $reflection->invoke($menu));
	}

	/**
	 * Test that getMenuTitle returns expected value
	 *
	 * @return void
	 */
	public function testGetMenuTitleReturnsExpectedValue(): void
	{
		$menu = new ConcreteAdminMenu();

		$reflection = new \ReflectionMethod($menu, 'getMenuTitle');

		$this->assertEquals('Test Menu', $reflection->invoke($menu));
	}

	/**
	 * Test that getCapability returns expected value
	 *
	 * @return void
	 */
	public function testGetCapabilityReturnsExpectedValue(): void
	{
		$menu = new ConcreteAdminMenu();

		$reflection = new \ReflectionMethod($menu, 'getCapability');

		$this->assertEquals('manage_options', $reflection->invoke($menu));
	}

	/**
	 * Test that getMenuSlug returns expected value
	 *
	 * @return void
	 */
	public function testGetMenuSlugReturnsExpectedValue(): void
	{
		$menu = new ConcreteAdminMenu();

		$reflection = new \ReflectionMethod($menu, 'getMenuSlug');

		$this->assertEquals('test-menu', $reflection->invoke($menu));
	}

	/**
	 * Test that processAdminMenu calls getViewComponent with correct attributes
	 *
	 * @return void
	 */
	public function testProcessAdminMenuSetsAttributesAndRendersView(): void
	{
		Functions\when('ob_start')->justReturn();
		Functions\when('wp_nonce_field')->justReturn();
		Functions\when('ob_get_clean')->justReturn('nonce_field_html');

		$menu = new ConcreteAdminMenu();

		$this->expectOutputString('');
		$menu->processAdminMenu([]);
	}
}

/**
 * Concrete implementation of AbstractAdminMenu for testing
 */
class ConcreteAdminMenu extends AbstractAdminMenu
{
	/**
	 * Get the title
	 *
	 * @return string
	 */
	protected function getTitle(): string
	{
		return 'Test Admin Menu';
	}

	/**
	 * Get the menu title
	 *
	 * @return string
	 */
	protected function getMenuTitle(): string
	{
		return 'Test Menu';
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
		return 'test-menu';
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
