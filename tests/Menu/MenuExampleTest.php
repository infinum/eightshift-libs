<?php

namespace Tests\Unit\Menu;

use Brain\Monkey;
use EightshiftBoilerplate\Menu\MenuExample;

/**
 * Mock before tests.
 */
beforeEach(function () {
	Monkey\setUp();
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	Monkey\tearDown();
});


test('Register method will call init hook', function () {
	(new MenuExample())->register();

	$this->assertSame(11, has_action('after_setup_theme', 'EightshiftBoilerplate\Menu\MenuExample->registerMenuPositions()'));
});
