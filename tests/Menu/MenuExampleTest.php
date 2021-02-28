<?php

namespace Tests\Unit\Menu;

use Brain\Monkey;
use EightshiftBoilerplate\Menu\MenuExample;
use Brain\Monkey\Functions;
use EightshiftLibs\Menu\AbstractMenu;

use function Tests\setupMocks;

beforeEach(function() {
	Monkey\setUp();
	setupMocks();

	$this->example = new MenuExample();
});

afterEach(function() {
	Monkey\tearDown();
});


test('Register method will call init hook', function () {
	$this->example->register();

	$this->assertSame(11, has_action('after_setup_theme', 'EightshiftBoilerplate\Menu\MenuExample->registerMenuPositions()'));
});


test('Menu example contains correct menu position', function () {
	$menuPositions = $this->example->getMenuPositions();

	$this->assertIsArray($menuPositions, 'Menu positions should be an array');
	$this->assertArrayHasKey('header_main_nav', $menuPositions);
});


test('Bem menu returns correct default arguments', function() {
	// Mock wp_nav_menu() and walker.
	Functions\when('wp_nav_menu')->returnArg();
	Functions\when('has_nav_menu')->justReturn(true);

	$menu = $this->example::bemMenu();

	$this->assertIsArray($menu, 'Menu arguments expected');

	$this->assertArrayHasKey('container', $menu, 'Key "container" not found in the arguments array.');
	$this->assertArrayHasKey('items_wrap', $menu, 'Key "items_wrap" not found in the arguments array.');
	$this->assertArrayHasKey('echo', $menu, 'Key "echo" not found in the arguments array.');
	$this->assertArrayHasKey('walker', $menu, 'Key "walker" not found in the arguments array.');
	$this->assertArrayHasKey('theme_location', $menu, 'Key "theme_location" not found in the arguments array.');

	$this->assertSame(false, $menu['container'], 'container argument should be false');
	$this->assertSame('<ul class="main-menu">%3$s</ul>', $menu['items_wrap']);
	$this->assertSame(true, $menu['echo'], 'echo argument should be true');
	$this->assertSame(null, $menu['walker'], 'walker argument should be null');
	$this->assertSame($this->example::MAIN_MENU, $menu['theme_location'], 'theme_location argument should be a "header_main_nav" string');
});


test('Bem menu returns correctly set arguments where modifier is an array', function() {
	// Mock wp_nav_menu() and walker.
	Functions\when('wp_nav_menu')->returnArg();
	Functions\when('has_nav_menu')->justReturn(false);

	$menu = $this->example::bemMenu($this->example::MAIN_MENU, 'main-footer-menu', 'js-menu', ['is-modified', 'main']);

	$this->assertIsArray($menu, 'Menu arguments expected');

	$this->assertArrayHasKey('container', $menu, 'Key "container" not found in the arguments array.');
	$this->assertArrayHasKey('items_wrap', $menu, 'Key "items_wrap" not found in the arguments array.');
	$this->assertArrayHasKey('echo', $menu, 'Key "echo" not found in the arguments array.');
	$this->assertArrayHasKey('walker', $menu, 'Key "walker" not found in the arguments array.');
	$this->assertArrayHasKey('menu', $menu, 'Key "menu" not found in the arguments array.');

	$this->assertSame(false, $menu['container'], 'container argument should be false');
	$this->assertSame('<ul class="main-footer-menu is-modified main js-menu">%3$s</ul>', $menu['items_wrap']);
	$this->assertSame(true, $menu['echo'], 'echo argument should be true');
	$this->assertSame(null, $menu['walker'], 'walker argument should be null');
	$this->assertSame($this->example::MAIN_MENU, $menu['menu'], 'menu argument should be a "header_main_nav" string');
});


test('Bem menu returns correctly set arguments where modifier is a string', function() {
	// Mock wp_nav_menu() and walker.
	Functions\when('wp_nav_menu')->returnArg();
	Functions\when('has_nav_menu')->justReturn(false);

	$menu = $this->example::bemMenu($this->example::MAIN_MENU, 'main-footer-menu', '', 'is-modified');

	$this->assertIsArray($menu, 'Menu arguments expected');

	$this->assertArrayHasKey('container', $menu, 'Key "container" not found in the arguments array.');
	$this->assertArrayHasKey('items_wrap', $menu, 'Key "items_wrap" not found in the arguments array.');
	$this->assertArrayHasKey('echo', $menu, 'Key "echo" not found in the arguments array.');
	$this->assertArrayHasKey('walker', $menu, 'Key "walker" not found in the arguments array.');
	$this->assertArrayHasKey('menu', $menu, 'Key "menu" not found in the arguments array.');

	$this->assertSame(false, $menu['container'], 'container argument should be false');
	$this->assertSame('<ul class="main-footer-menu is-modified">%3$s</ul>', $menu['items_wrap']);
	$this->assertSame(true, $menu['echo'], 'echo argument should be true');
	$this->assertSame(null, $menu['walker'], 'walker argument should be null');
	$this->assertSame($this->example::MAIN_MENU, $menu['menu'], 'menu argument should be a "header_main_nav" string');
});


test('Extending the abstract menu works', function() {
	// Set up a side-affect.
	putenv('MENU_ABSTRACTED=false');

	// Mock the menu abstraction.
	$menu = new class extends AbstractMenu {
		public function register(): void {
			echo 'Registered';
		}
	};

	// Introduce a side-affect we can check.
	Functions\when('register_nav_menus')->alias(function() {
		putenv('MENU_ABSTRACTED=true');
	});

	$menu->registerMenuPositions();

	$this->assertSame(getenv('MENU_ABSTRACTED'), 'true', 'Calling void method register_nav_menus caused no sideaffects');

	$this->assertIsArray($menu->getMenuPositions(), 'Menu positions getter must return an array');
	$this->assertEmpty($menu->getMenuPositions(), 'Unset menu positions getter must return an empty array');
});


