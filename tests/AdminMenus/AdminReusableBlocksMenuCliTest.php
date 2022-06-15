<?php

namespace Tests\Unit\CustomPostType;

use EightshiftLibs\AdminMenus\AdminReusableBlocksMenuCli;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->adminReusableBlocksMenuCli = new AdminReusableBlocksMenuCli('boilerplate');
});

afterEach(function () {
	setAfterEach();
});

test('Admin reusable blocks menu CLI command will correctly copy the admin reusable blocks menu example class with defaults', function () {
	$mock = $this->adminReusableBlocksMenuCli;
	$mock([], $mock->getDefaultArgs());

	// Check the output dir if the generated method is correctly generated.
	$output = \file_get_contents(\dirname(__FILE__, 3) . '/cliOutput/src/AdminMenus/AdminReusableBlocksMenu.php');

	expect($output)
		->not->toBeEmpty()
		->toContain('class AdminReusableBlocksMenu extends AbstractAdminMenu')
		->toContain('Reusable Blocks')
		->not->toContain('product');
});

test('Admin reusable blocks menu CLI command will correctly copy the admin reusable blocks menu class with set arguments', function () {
	$mock = $this->adminReusableBlocksMenuCli;
	$mock([], [
		'title' => 'Reusable Blocks',
		'menu_title' => 'Reusable Blocks',
		'capability' => 'edit_posts',
		'menu_slug' => 'edit.php?post_type=wp_block',
		'menu_icon' => 'dashicons-editor-table',
	]);

	// Check the output dir if the generated method is correctly generated.
	$output = \file_get_contents(\dirname(__FILE__, 3) . '/cliOutput/src/AdminMenus/AdminReusableBlocksMenu.php');

	expect($output)
		->not->toBeEmpty()
		->toContain('class AdminReusableBlocksMenu extends AbstractAdminMenu')
		->toContain('Reusable Blocks')
		->toContain('edit_posts')
		->toContain('4')
		->toContain('dashicons-editor-table')
		->not->toContain('dashicons-analytics');
});

test('Admin reusable blocks menu CLI documentation is correct', function () {
	expect($this->adminReusableBlocksMenuCli->getDoc())->toBeArray();
});
