<?php

namespace Tests\Unit\AdminMenus;

use Brain\Monkey;
use Brain\Monkey\Actions;
use Brain\Monkey\Functions;
use EightshiftBoilerplate\AdminMenus\AdminMenuExample;

use function Tests\setupUnitTestMocks;

beforeEach(function() {
	Monkey\setUp();
	setupUnitTestMocks();

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


test('processAdminMenu will render the component', function() {
	Functions\when('wp_nonce_field')->justReturn('nonce');

	ob_start();
	$this->example->processAdminMenu([]);
	$contents = ob_get_clean();

	expect($contents)
		->not->toBeEmpty()
		->toBe('<div>Hi!</div>');
});

test('processAdminMenu will render the error if the component is missing', function() {
	Functions\when('wp_nonce_field')->justReturn('nonce');

	$faultyExample = new class extends AdminMenuExample {
		protected function getViewComponent(): string
		{
			return 'test';
		}
	};

	ob_start();
	$faultyExample->processAdminMenu([]);
	$contents = ob_get_clean();

	expect($contents)
		->not->toBeEmpty()
		->toContain('<pre>Unable to locate component by path:');
});


test('getIcon will return the default icon', function() {

	// We shouldn't do this, but the add_menu_page is called in a closure.
	$method = new \ReflectionMethod('EightshiftBoilerplate\\AdminMenus\\AdminMenuExample', 'getIcon');
    $method->setAccessible(true);

	expect($method->invoke(new AdminMenuExample()))
		->not->toBeEmpty()
		->toBe('dashicons-admin-generic');
});
