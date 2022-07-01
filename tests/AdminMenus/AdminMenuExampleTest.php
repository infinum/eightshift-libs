<?php

namespace Tests\Unit\AdminMenus;

use Brain\Monkey\Actions;
use Brain\Monkey\Functions;
use EightshiftBoilerplate\AdminMenus\AdminMenuExample;

use function Tests\buildTestBlocks;
use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->mock = new AdminMenuExample();
});

afterEach(function () {
	setAfterEach();

	unset($this->mock);
});

//---------------------------------------------------------------------------------//

test('register will load all hooks', function () {

	$this->mock->register();

	expect(has_action('admin_menu', 'EightshiftBoilerplate\AdminMenus\AdminMenuExample->callback()'))
		->toBe(10);
});

//---------------------------------------------------------------------------------//

test('callback will load correct method', function () {

	$action = 'add_menu_page';
	Functions\when($action)->justReturn(putenv("ES_SIDEAFFECT_1={$action}"));

	$this->mock->callback();

	expect(getenv('ES_SIDEAFFECT_1'))->toEqual($action);
});

//---------------------------------------------------------------------------------//

test('processAdminMenu will echo component view', function () {

	buildTestBlocks();

	ob_start();
	$this->mock->processAdminMenu([]);
	$contents = ob_get_clean();

	expect($contents)
		->not->toBeEmpty()
		->toBe('<div>Hi!</div>');
});

test('processAdminMenu will echo error if component is missing', function () {
	$mock = new class extends AdminMenuExample {
		protected function getViewComponent(): string
		{
			return 'missing';
		}
	};

	buildTestBlocks();

	ob_start();
	$mock->processAdminMenu([]);
	$contents = ob_get_clean();

	expect($contents)
		->not->toBeEmpty()
		->toContain('<pre>Unable to locate component by path:');
});

// test('processAdminMenu will render the error if the component is missing', function() {
// 	Functions\when('wp_nonce_field')->justReturn('nonce');

// 	$faultyExample = new class extends AdminMenuExample {
// 		protected function getViewComponent(): string
// 		{
// 			return 'test';
// 		}
// 	};

// 	ob_start();
// 	$faultyExample->processAdminMenu([]);
// 	$contents = ob_get_clean();

// 	expect($contents)
// 		->not->toBeEmpty()
// 		->toContain('<pre>Unable to locate component by path:');
// });


// test('getIcon will return the default icon', function() {

// 	// We shouldn't do this, but the add_menu_page is called in a closure.
// 	$method = new \ReflectionMethod('EightshiftBoilerplate\\AdminMenus\\AdminMenuExample', 'getIcon');
// 	$method->setAccessible(true);

// 	expect($method->invoke(new AdminMenuExample()))
// 		->not->toBeEmpty()
// 		->toBe('%menu_icon%');
// });
