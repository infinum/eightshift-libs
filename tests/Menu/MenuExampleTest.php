<?php

namespace Tests\Unit\Menu;

use Brain\Monkey;
use EightshiftBoilerplate\Menu\MenuExample;

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
