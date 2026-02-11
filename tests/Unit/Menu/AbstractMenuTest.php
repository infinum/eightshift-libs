<?php

/**
 * Tests for AbstractMenu class
 *
 * @package EightshiftLibs\Tests\Unit\Menu
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\Menu;

use Brain\Monkey;
use Brain\Monkey\Functions;
use EightshiftLibs\Menu\AbstractMenu;
use EightshiftLibs\Menu\MenuPositionsInterface;
use EightshiftLibs\Services\ServiceInterface;
use EightshiftLibs\Tests\BaseTestCase;

/**
 * AbstractMenuTest class
 */
class AbstractMenuTest extends BaseTestCase
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
	 * Test that AbstractMenu implements ServiceInterface
	 *
	 * @return void
	 */
	public function testImplementsServiceInterface(): void
	{
		$menu = new ConcreteMenu();

		$this->assertInstanceOf(ServiceInterface::class, $menu);
	}

	/**
	 * Test that AbstractMenu implements MenuPositionsInterface
	 *
	 * @return void
	 */
	public function testImplementsMenuPositionsInterface(): void
	{
		$menu = new ConcreteMenu();

		$this->assertInstanceOf(MenuPositionsInterface::class, $menu);
	}

	/**
	 * Test that bemMenu static method exists
	 *
	 * @return void
	 */
	public function testBemMenuMethodExists(): void
	{
		$this->assertTrue(\method_exists(AbstractMenu::class, 'bemMenu'));
	}

	/**
	 * Test that getMenuPositions returns empty array by default
	 *
	 * @return void
	 */
	public function testGetMenuPositionsReturnsEmptyArrayByDefault(): void
	{
		$menu = new ConcreteMenu();

		$this->assertEquals([], $menu->getMenuPositions());
	}

	/**
	 * Test that registerMenuPositions calls register_nav_menus with getMenuPositions result
	 *
	 * @return void
	 */
	public function testRegisterMenuPositionsCallsRegisterNavMenus(): void
	{
		Functions\expect('register_nav_menus')
			->once()
			->with([]);

		$menu = new ConcreteMenu();
		$menu->registerMenuPositions();
	}

	/**
	 * Test that register method adds after_setup_theme action
	 *
	 * @return void
	 */
	public function testRegisterAddsAfterSetupThemeAction(): void
	{
		Functions\expect('add_action')
			->once()
			->with('after_setup_theme', \Mockery::type('array'));

		$menu = new ConcreteMenu();
		$menu->register();
	}
}

/**
 * Concrete implementation of AbstractMenu for testing
 */
class ConcreteMenu extends AbstractMenu
{
	/**
	 * Register the service
	 *
	 * @return void
	 */
	public function register(): void
	{
		\add_action('after_setup_theme', [$this, 'registerMenuPositions']);
	}


}
