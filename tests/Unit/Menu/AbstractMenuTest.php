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

		// Stub Walker_Nav_Menu if not defined (required by BemMenuWalker).
		if (!\class_exists('Walker_Nav_Menu')) {
			// phpcs:ignore
			eval('class Walker_Nav_Menu {
				public $db_fields = ["id" => "ID", "parent" => "menu_item_parent"];
				public function __construct() {}
				public function display_element($element, &$children_elements, $max_depth, $depth, $args, &$output) {}
			}');
		}
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

	/**
	 * Test bemMenu returns empty string when nav menu location not registered
	 *
	 * @return void
	 */
	public function testBemMenuReturnsEmptyStringWhenNoNavMenu(): void
	{
		Functions\when('has_nav_menu')->justReturn(false);

		$result = ConcreteMenu::bemMenu('main_menu', 'main-menu');

		$this->assertSame('', $result);
	}

	/**
	 * Test bemMenu calls wp_nav_menu when location exists
	 *
	 * @return void
	 */
	public function testBemMenuCallsWpNavMenuWhenLocationExists(): void
	{
		Functions\when('has_nav_menu')->justReturn(true);

		Functions\expect('wp_nav_menu')
			->once()
			->with(\Mockery::on(function ($args) {
				return $args['theme_location'] === 'footer_menu'
					&& $args['echo'] === true
					&& \str_contains($args['items_wrap'], 'footer-nav');
			}))
			->andReturn('<nav>menu</nav>');

		$result = ConcreteMenu::bemMenu('footer_menu', 'footer-nav');

		$this->assertSame('<nav>menu</nav>', $result);
	}

	/**
	 * Test bemMenu with string CSS modifiers
	 *
	 * @return void
	 */
	public function testBemMenuWithStringModifiers(): void
	{
		Functions\when('has_nav_menu')->justReturn(true);

		Functions\expect('wp_nav_menu')
			->once()
			->with(\Mockery::on(function ($args) {
				return \str_contains($args['items_wrap'], 'is-active');
			}))
			->andReturn('<nav>menu</nav>');

		ConcreteMenu::bemMenu('main_menu', 'main-menu', '', 'is-active');
	}

	/**
	 * Test bemMenu with array CSS modifiers
	 *
	 * @return void
	 */
	public function testBemMenuWithArrayModifiers(): void
	{
		Functions\when('has_nav_menu')->justReturn(true);

		Functions\expect('wp_nav_menu')
			->once()
			->with(\Mockery::on(function ($args) {
				return \str_contains($args['items_wrap'], 'is-active is-open');
			}))
			->andReturn('<nav>menu</nav>');

		ConcreteMenu::bemMenu('main_menu', 'main-menu', '', ['is-active', 'is-open']);
	}

	/**
	 * Test bemMenu with parent class
	 *
	 * @return void
	 */
	public function testBemMenuWithParentClass(): void
	{
		Functions\when('has_nav_menu')->justReturn(true);

		Functions\expect('wp_nav_menu')
			->once()
			->with(\Mockery::on(function ($args) {
				return \str_contains($args['items_wrap'], 'parent-class');
			}))
			->andReturn('<nav>menu</nav>');

		ConcreteMenu::bemMenu('main_menu', 'main-menu', 'parent-class');
	}

	/**
	 * Test bemMenu with outputMenu=false
	 *
	 * @return void
	 */
	public function testBemMenuWithOutputMenuFalse(): void
	{
		Functions\when('has_nav_menu')->justReturn(true);

		Functions\expect('wp_nav_menu')
			->once()
			->with(\Mockery::on(function ($args) {
				return $args['echo'] === false;
			}))
			->andReturn('<nav>returned menu</nav>');

		$result = ConcreteMenu::bemMenu('main_menu', 'main-menu', '', '', false);

		$this->assertSame('<nav>returned menu</nav>', $result);
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
