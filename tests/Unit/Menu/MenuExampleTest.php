<?php

namespace Tests\Unit\Menu;

use Brain\Monkey;
use Brain\Monkey\Functions;
use EightshiftBoilerplate\Menu\MenuExample;
use EightshiftLibs\Menu\AbstractMenu;

use function Tests\mock;

beforeEach(function() {
	mock('Walker_Nav_Menu');

	Functions\when('register_nav_menus')->alias(function($args) {
		$position = \array_key_first($args);

		putenv("REGISTERED_MENU={$position}");
	});

	Functions\when('wp_nav_menu')->alias(function($args) {
		return $args;
	});

	$this->example = new MenuExample();
});

afterEach(function() {
	unset($this->example);
	putenv('REGISTERED_MENU');
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


test('Register menu positions will work', function () {
	$this->example->registerMenuPositions();

	$this->assertSame(\getenv('REGISTERED_MENU'), 'header_main_nav');
});


test('Bem menu will output menu', function () {
	Functions\when('has_nav_menu')->justReturn(true);

	$menu = $this->example::bemMenu();

	$this->assertIsArray($menu);
	$this->assertArrayHasKey('theme_location', $menu, 'Menu arguments is missing the theme_location key');
	$this->assertArrayHasKey('container', $menu, 'Menu arguments is missing the container key');
	$this->assertArrayHasKey('items_wrap', $menu, 'Menu arguments is missing the items_wrap key');
	$this->assertArrayHasKey('echo', $menu, 'Menu arguments is missing the echo key');
	$this->assertArrayHasKey('walker', $menu, 'Menu arguments is missing the walker key');
	$this->assertSame('EightshiftLibs\Menu\BemMenuWalker', \get_class($menu['walker']));
});


test('Bem menu will output menu with string css modifiers', function () {
	Functions\when('has_nav_menu')->justReturn(true);

	$menu = $this->example::bemMenu('main_menu', 'main-menu', '', 'mobile', true);

	$this->assertSame('<ul class=" main-menu mobile">%3$s</ul>', $menu['items_wrap']);
});


test('Bem menu will output menu with array css modifiers', function () {
	Functions\when('has_nav_menu')->justReturn(true);

	$menu = $this->example::bemMenu('main_menu', 'main-menu', '', ['modifier', 'modifier2'], true);

	$this->assertSame('<ul class=" main-menu modifier modifier2">%3$s</ul>', $menu['items_wrap']);
});


test('Bem menu will output empty string in case the location is missing', function () {
	Functions\when('has_nav_menu')->justReturn(false);

	$menu = $this->example::bemMenu('main_menu', 'main-menu', '', ['modifier', 'modifier2'], true);

	$this->assertSame('', $menu);
});

test('Menu positions are empty by default', function() {
	$menu = new class extends AbstractMenu {
		public function register():void {}
	};

	$positions = $menu->getMenuPositions();

	$this->assertIsArray($positions);
	$this->assertEmpty($positions);
});
