<?php

namespace Tests\Unit\Menu;

use Brain\Monkey\Functions;
use EightshiftLibs\Menu\AbstractMenu;
use EightshiftLibs\Menu\MenuCli;
use Infinum\Menu\Menu;

use function Tests\getMockArgs;
use function Tests\reqOutputFiles;

beforeEach(function() {
	Functions\when('register_nav_menus')->alias(function($args) {
		$position = \array_key_first($args);

		putenv("REGISTERED_MENU={$position}");
	});

	Functions\when('wp_nav_menu')->alias(function($args) {
		return $args;
	});

	$menuCliMock = new MenuCli('boilerplate');
	$menuCliMock([], getMockArgs($menuCliMock->getDefaultArgs()));

	reqOutputFiles(
		'Menu/Menu.php',
	);
});

afterEach(function() {
	putenv('REGISTERED_MENU');
});


test('Register method will call init hook', function () {
	(new Menu())->register();

	$this->assertSame(11, has_action('after_setup_theme', 'Infinum\Menu\Menu->registerMenuPositions()'));
});


test('Menu example contains correct menu position', function () {
	$menuPositions = (new Menu())->getMenuPositions();

	$this->assertIsArray($menuPositions, 'Menu positions should be an array');
	$this->assertArrayHasKey('header_main_nav', $menuPositions);
});


test('Register menu positions will work', function () {
	(new Menu())->registerMenuPositions();

	$this->assertSame(\getenv('REGISTERED_MENU'), 'header_main_nav');
});

test('Menu positions are empty by default', function() {
	$menu = new class extends AbstractMenu {
		public function register():void {}
	};

	$positions = $menu->getMenuPositions();

	$this->assertIsArray($positions);
	$this->assertEmpty($positions);
});
