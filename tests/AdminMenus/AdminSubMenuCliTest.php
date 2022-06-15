<?php

namespace Tests\Unit\CustomPostType;

use EightshiftLibs\AdminMenus\AdminSubMenuCli;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->adminSubMenuCli = new AdminSubMenuCli('boilerplate');
});

afterEach(function () {
	setAfterEach();
});

test('Admin submenu CLI command will correctly copy the admin menu example class with defaults', function () {
	$mock = $this->adminSubMenuCli;
	$mock([], $mock->getDefaultArgs());

	// Check the output dir if the generated method is correctly generated.
	$output = \file_get_contents(\dirname(__FILE__, 3) . '/cliOutput/src/AdminMenus/TestAdminTitleAdminSubMenu.php');

	expect($output)
		->toContain('class TestAdminTitleAdminSubMenu extends AbstractAdminSubMenu', 'Test Admin Title')
		->not->toContain('product');
});

test('Admin submenu CLI command will correctly copy the admin menu class with set arguments', function () {
	$mock = $this->adminSubMenuCli;
	$mock([], [
		'parent_slug' => 'reusable-blocks',
		'title' => 'Options',
		'menu_title' => 'Options',
		'capability' => 'edit_reusable_blocks',
		'menu_slug' => 'reusable-block-options',
	]);

	// Check the output dir if the generated method is correctly generated.
	$output = \file_get_contents(\dirname(__FILE__, 3) . '/cliOutput/src/AdminMenus/ReusableBlockOptionsAdminSubMenu.php');

	expect($output)
		->toContain('class ReusableBlockOptionsAdminSubMenu extends AbstractAdminSubMenu', 'Options', 'edit_reusable_blocks')
		->not->toContain('dashicons-analytics');
});

test('Admin submenu CLI documentation is correct', function () {
	expect($this->adminSubMenuCli->getDoc())->toBeArray();
});
