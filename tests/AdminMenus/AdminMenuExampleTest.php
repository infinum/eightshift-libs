<?php

namespace Tests\Unit\AdminMenus;

use Brain\Monkey;
use Brain\Monkey\Actions;
use EightshiftBoilerplate\AdminMenus\AdminMenuExample;

use function Tests\setupMocks;

beforeEach(function() {
	Monkey\setUp();
	setupMocks();

	$this->example = new AdminMenuExample();
});

afterEach(function() {
	Monkey\tearDown();
});


test('Register method will call admin_menu hook', function () {
	Actions\expectAdded('admin_menu')->with(\Mockery::type('Closure'));

	$this->example->register();

	$this->assertSame(10, has_action('admin_menu', 'function()'));
});
