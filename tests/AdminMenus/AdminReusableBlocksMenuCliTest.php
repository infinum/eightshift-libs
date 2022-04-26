<?php

namespace Tests\Unit\CustomPostType;

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
	$generatedCPT = \file_get_contents(\dirname(__FILE__, 3) . '/cliOutput/src/AdminMenus/AdminReusableBlocksMenu.php');

	expect($generatedCPT)
		->not->toBeEmpty()
		->toContain('class AdminReusableBlocksMenu extends AbstractAdminMenu');

	expect($generatedCPT)
		->not->toBeEmpty()
		->toContain('Reusable Blocks');

	expect($generatedCPT)
		->not->toBeEmpty()
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
	$generatedCPT = \file_get_contents(\dirname(__FILE__, 3) . '/cliOutput/src/AdminMenus/AdminReusableBlocksMenu.php');

	expect($generatedCPT)
		->not->toBeEmpty()
		->toContain('class AdminReusableBlocksMenu extends AbstractAdminMenu');

	expect($generatedCPT)
		->not->toBeEmpty()
		->toContain('Reusable Blocks');

	expect($generatedCPT)
		->not->toBeEmpty()
		->toContain('edit_posts');

	expect($generatedCPT)
		->not->toBeEmpty()
		->toContain('4');

	expect($generatedCPT)
		->not->toBeEmpty()
		->toContain('dashicons-editor-table');

	expect($generatedCPT)
		->not->toBeEmpty()
		->not->toContain('dashicons-analytics');
});


test('Admin reusable blocks menu CLI documentation is correct', function () {
	$cpt = $this->cpt;

	$documentation = $cpt->getDoc();

	$key = 'shortdesc';

	expect($documentation)->toBeArray();
	expect($documentation)->toHaveKey($key);
	expect($documentation)->toHaveKey('synopsis');
	expect($documentation[$key])->toBe('Generates reusable blocks admin menu class file.');
});
