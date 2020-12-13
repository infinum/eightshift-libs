<?php

namespace Tests\Unit\Menu;

use Brain\Monkey\Functions;
use EightshiftLibs\Menu\MenuCli;

use function cli\err;
use function Tests\deleteCliOutput;

/**
 * Mock before tests.
 */
beforeEach(function () {
    $wpCliMock = \Mockery::mock('alias:WP_CLI');

	$wpCliMock
		->shouldReceive('success')
		->andReturnArg(0);

	$wpCliMock
		->shouldReceive('error')
		->andReturnArg(0);
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	$output = dirname(__FILE__, 3) . '/cliOutput';

	deleteCliOutput($output);
});


test('Menu CLI command will correctly copy the Menu class with defaults', function () {
	$menu = new MenuCli('boilerplate');
	$menu([], []);

	// Check the output dir if the generated method is correctly generated.
	$generatedMenu = file_get_contents(dirname(__FILE__, 3) . '/cliOutput/src/Menu/Menu.php');

	$this->assertStringContainsString('class Menu extends AbstractMenu', $generatedMenu);
	$this->assertStringContainsString('header_main_nav', $generatedMenu);
	$this->assertStringNotContainsString('rendom string', $generatedMenu);
});


test('Menu CLI command will correctly copy the Menu class with set arguments', function () {
	$menu = new MenuCli('boilerplate');
	$menu([], [
		'namespace' => 'CoolTheme',
	]);

	// Check the output dir if the generated method is correctly generated.
	$generatedMenu = file_get_contents(dirname(__FILE__, 3) . '/cliOutput/src/Menu/Menu.php');

	$this->assertStringContainsString('class Menu extends AbstractMenu', $generatedMenu);
	$this->assertStringContainsString('namespace CoolTheme\Menu;', $generatedMenu);
	$this->assertStringContainsString('header_main_nav', $generatedMenu);
	$this->assertStringNotContainsString('rendom string', $generatedMenu);
});


test('Menu CLI documentation is correct', function () {
	$menu = new MenuCli('boilerplate');

	$documentation = $menu->getDoc();

	$key = 'shortdesc';

	$this->assertIsArray($documentation);
	$this->assertArrayHasKey($key, $documentation);
	$this->assertArrayNotHasKey('synopsis', $documentation);
	$this->assertEquals('Generates menu class.', $documentation[$key]);
});
