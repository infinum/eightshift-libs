<?php

namespace Tests\Unit\Menu;

use EightshiftLibs\Menu\MenuCli;

use function Tests\deleteCliOutput;
use function Tests\mock;

/**
 * Mock before tests.
 */
beforeEach(function () {
    $wpCliMock = mock('alias:WP_CLI');

	$wpCliMock
		->shouldReceive('success')
		->andReturnArg(0);

	$wpCliMock
		->shouldReceive('error')
		->andReturnArg(0);

	$this->menu = new MenuCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	deleteCliOutput();
});


test('Menu CLI command will correctly copy the Menu class with defaults', function () {
	$menu = $this->menu;
	$menu([], []);

	// Check the output dir if the generated method is correctly generated.
	$generatedMenu = \file_get_contents(\dirname(__FILE__, 4) . '/cliOutput/src/Menu/Menu.php');
	$this->assertStringContainsString('class Menu extends AbstractMenu', $generatedMenu);
	$this->assertStringContainsString('header_main_nav', $generatedMenu);
	$this->assertStringNotContainsString('rendom string', $generatedMenu);
});


test('Menu CLI command will correctly copy the Menu class with set arguments', function () {
	$menu = $this->menu;
	$menu([], [
		'namespace' => 'CoolTheme',
	]);

	// Check the output dir if the generated method is correctly generated.
	$generatedMenu = \file_get_contents(\dirname(__FILE__, 4) . '/cliOutput/src/Menu/Menu.php');

	$this->assertStringContainsString('class Menu extends AbstractMenu', $generatedMenu);
	$this->assertStringContainsString('namespace CoolTheme\Menu;', $generatedMenu);
	$this->assertStringContainsString('header_main_nav', $generatedMenu);
	$this->assertStringNotContainsString('rendom string', $generatedMenu);
});


test('Menu CLI documentation is correct', function () {
	$menu = $this->menu;

	$documentation = $menu->getDoc();

	$key = 'shortdesc';

	$this->assertIsArray($documentation);
	$this->assertArrayHasKey($key, $documentation);
	$this->assertArrayNotHasKey('synopsis', $documentation);
	$this->assertSame('Generates menu class.', $documentation[$key]);
});

