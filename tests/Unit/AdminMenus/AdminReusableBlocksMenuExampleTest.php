<?php

namespace Tests\Unit\AdminMenus;

use Brain\Monkey;
use Brain\Monkey\Actions;
use EightshiftBoilerplate\AdminMenus\AdminReusableBlocksMenuExample;

use function Tests\setupMocks;

beforeEach(function() {
	Monkey\setUp();
	setupMocks();

	$this->example = new AdminReusableBlocksMenuExample();
});

afterEach(function() {
	Monkey\tearDown();
});


test('Register method will call admin_menu hook', function () {
	Actions\expectAdded('admin_menu')->with(\Mockery::type('Closure'));

	$this->example->register();

	$this->assertSame(10, has_action('admin_menu', 'function()'));
});


test('getMenuSlug will return the default menu slug', function() {

	// We shouldn't do this, but the add_menu_page is called in a closure.
	$method = new \ReflectionMethod('EightshiftBoilerplate\\AdminMenus\\AdminReusableBlocksMenuExample', 'getMenuSlug');
	$method->setAccessible(true);

	expect($method->invoke(new AdminReusableBlocksMenuExample()))
		->not->toBeEmpty()
		->toBe('edit.php?post_type=wp_block');
});


test('getIcon will return the default icon', function() {

	// We shouldn't do this, but the add_menu_page is called in a closure.
	$method = new \ReflectionMethod('EightshiftBoilerplate\\AdminMenus\\AdminReusableBlocksMenuExample', 'getIcon');
	$method->setAccessible(true);

	expect($method->invoke(new AdminReusableBlocksMenuExample()))
		->not->toBeEmpty()
		->toBe('dashicons-editor-table');
});
