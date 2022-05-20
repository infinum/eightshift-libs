<?php

namespace Tests\Unit\CustomPostType;

use EightshiftLibs\AdminMenus\AdminMenuCli;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->adminMenuCli = new AdminMenuCli('boilerplate');
});

afterEach(function () {
	setAfterEach();
});


test('Admin menu CLI command will correctly copy the admin menu example class with defaults', function () {
	$mock = $this->adminMenuCli;
	$mock([], $mock->getDevelopArgs([]));

	// Check the output dir if the generated method is correctly generated.
	$output = \file_get_contents(\dirname(__FILE__, 3) . '/cliOutput/src/AdminMenus/TestTitleAdminMenu.php');

	expect($output)
		->toContain('class TestTitleAdminMenu extends AbstractAdminMenu', 'Test Title')
		->not->toContain('product');
});

test('Admin menu CLI command will correctly copy the admin menu class with set arguments', function () {
	$mock = $this->adminMenuCli;
	$mock([], [
		'title' => 'Reusable Blocks',
		'menu_title' => 'Reusable Blocks',
		'capability' => 'edit_reusable_blocks',
		'menu_slug' => 'reusable-blocks',
		'menu_icon' => 'dashicons-editor-table',
	]);

	// Check the output dir if the generated method is correctly generated.
	$output = \file_get_contents(\dirname(__FILE__, 3) . '/cliOutput/src/AdminMenus/ReusableBlocksAdminMenu.php');

	expect($output)
		->toContain('class ReusableBlocksAdminMenu extends AbstractAdminMenu', 'Reusable Blocks', 'edit_reusable_blocks', '100', 'dashicons-editor-table')
		->not->toContain('dashicons-analytics');
});

test('Admin menu CLI documentation is correct', function () {
	expect($this->adminMenuCli->getDoc())->toBeArray();
});
