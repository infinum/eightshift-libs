<?php

namespace Tests\Unit\Menu;

use Brain\Monkey;
use EightshiftBoilerplate\Menu\MenuExample;

/**
 * Mock before tests.
 */
beforeAll(function () {
	Monkey\setUp();
});

/**
 * Cleanup after tests.
 */
afterAll(function () {
	Monkey\tearDown();
});


test('Register method will call init hook', function () {
	(new MenuExample())->register();

	$this->assertSame(11, has_action('after_setup_theme', 'EightshiftBoilerplate\Menu\MenuExample->registerMenuPositions()'));
});


test('Menu example contains correct menu position', function () {
	$menuPositions = (new MenuExample())->getMenuPositions();

	$this->assertIsArray($menuPositions, 'Menu positions should be an array');
	$this->assertArrayHasKey('header_main_nav', $menuPositions);
});
