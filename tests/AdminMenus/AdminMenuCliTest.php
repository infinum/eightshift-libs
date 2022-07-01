<?php

namespace Tests\Unit\CustomPostType;

use EightshiftLibs\AdminMenus\AdminMenuCli;
use EightshiftLibs\Helpers\Components;

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
	$mock([], $mock->getDefaultArgs());

	// Check the output dir if the generated method is correctly generated.
	$sep = \DIRECTORY_SEPARATOR;
	$mock = \file_get_contents(Components::getProjectPaths('cliOutput', "src{$sep}AdminMenus{$sep}ExampleMenuSlugAdminMenu.php"));

	expect($mock)
		->toContain(
			'class ExampleMenuSlugAdminMenu extends AbstractAdminMenu',
			 'Admin Title',
			 'Admin Menu Title',
			 'edit_posts',
			 'example-menu-slug',
			 'dashicons-admin-generic',
		)
		->not->toContain(
			'%title%',
			'%menu_title%',
			'%capability%',
			'%menu_slug%',
			'%menu_icon%',
			'%menu_position%',
		);
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
	$sep = \DIRECTORY_SEPARATOR;
	$mock = \file_get_contents(Components::getProjectPaths('cliOutput', "src{$sep}AdminMenus{$sep}ReusableBlocksAdminMenu.php"));

	expect($mock)
		->toContain(
			'class ReusableBlocksAdminMenu extends AbstractAdminMenu',
			'Reusable Blocks',
			'Reusable Blocks',
			'edit_reusable_blocks',
			'reusable-blocks',
			'dashicons-editor-table',
		)
		->not->toContain(
			'%title%',
			'%menu_title%',
			'%capability%',
			'%menu_slug%',
			'%menu_icon%',
			'%menu_position%',
		);
});

test('Admin menu CLI documentation is correct', function () {
	expect($this->adminMenuCli->getDoc())->toBeArray();
});
