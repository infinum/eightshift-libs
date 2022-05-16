<?php

namespace Tests\Unit\AdminMenus;

use EightshiftLibs\AdminMenus\AdminReusableBlocksMenuCli;

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

	$this->cpt = new AdminReusableBlocksMenuCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	deleteCliOutput();
});


test('Admin reusable blocks menu CLI command will correctly copy the admin reusable blocks menu example class with defaults', function () {
	$cpt = $this->cpt;
	$cpt([], $cpt->getDevelopArgs([]));

	// Check the output dir if the generated method is correctly generated.
	$generatedCPT = \file_get_contents(\dirname(__FILE__, 4) . '/cliOutput/src/AdminMenus/AdminReusableBlocksMenu.php');

	expect($generatedCPT)
		->not->toBeEmpty()
		->toContain('class AdminReusableBlocksMenu extends AbstractAdminMenu')
		->toContain('Reusable Blocks')
		->not->toContain('product');
});


test('Admin reusable blocks menu CLI command will correctly copy the admin reusable blocks menu class with set arguments', function () {
	$cpt = $this->cpt;
	$cpt([], [
		'title' => 'Reusable Blocks',
		'menu_title' => 'Reusable Blocks',
		'capability' => 'edit_posts',
		'menu_slug' => 'edit.php?post_type=wp_block',
		'menu_icon' => 'dashicons-editor-table',
	]);

	// Check the output dir if the generated method is correctly generated.
	$generatedCPT = \file_get_contents(\dirname(__FILE__, 4) . '/cliOutput/src/AdminMenus/AdminReusableBlocksMenu.php');

	expect($generatedCPT)
		->not->toBeEmpty()
		->toContain('class AdminReusableBlocksMenu extends AbstractAdminMenu')
		->toContain('Reusable Blocks')
		->toContain('edit_posts')
		->toContain('4')
		->toContain('dashicons-editor-table')
		->not->toContain('dashicons-analytics');
});


test('Admin reusable blocks menu CLI documentation is correct', function () {
	$cpt = $this->cpt;

	$documentation = $cpt->getDoc();

	$key = 'shortdesc';

	expect($documentation)
		->toBeArray()
		->toHaveKey($key)
		->toHaveKey('synopsis');

	expect($documentation[$key])->toBe('Generates reusable blocks admin menu class file.');
});
