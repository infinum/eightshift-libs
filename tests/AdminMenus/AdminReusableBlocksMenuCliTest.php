<?php

namespace Tests\Unit\CustomPostType;

use EightshiftLibs\AdminMenus\AdminReusableBlocksMenuCli;
use EightshiftLibs\Cli\ParentGroups\CliCreate;
use EightshiftLibs\Helpers\Components;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->mock = new AdminReusableBlocksMenuCli('boilerplate');
});

afterEach(function () {
	setAfterEach();

	unset($this->mock);
});

//---------------------------------------------------------------------------------//

test('getCommandParentName will return correct value', function () {
	expect($this->mock->getCommandParentName())
		->toBeString()
		->toEqual(CliCreate::COMMAND_NAME);
});

//---------------------------------------------------------------------------------//

test('getCommandName will return correct value', function () {
	expect($this->mock->getCommandName())
		->toBeString()
		->toEqual('admin-reusable-blocks-menu');
});

//---------------------------------------------------------------------------------//

test('getDefaultArgs will return correct array', function () {
	expect($this->mock->getDefaultArgs())
		->toBeArray()
		->toMatchArray([
			'title' => 'Reusable Blocks',
			'menu_title' => 'Reusable Blocks',
			'capability' => 'edit_posts',
			'menu_icon' => 'dashicons-admin-table',
			'menu_position' => 4,
		]);
});

//---------------------------------------------------------------------------------//

test('getDoc will return correct array', function () {
	$docs = $this->mock->getDoc();

	expect($docs)
		->toBeArray()
		->toHaveKeys(['shortdesc', 'synopsis', 'longdesc'])
		->and(count($docs['synopsis']))->toEqual(5)
		->and($docs['synopsis'][0]['name'])->toEqual('title')
		->and($docs['synopsis'][1]['name'])->toEqual('menu_title')
		->and($docs['synopsis'][2]['name'])->toEqual('capability')
		->and($docs['synopsis'][3]['name'])->toEqual('menu_icon')
		->and($docs['synopsis'][4]['name'])->toEqual('menu_position');
});

//---------------------------------------------------------------------------------//

test('__invoke will will correctly copy example class with default args', function () {
	$mock = $this->mock;
	$mock([], $this->mock->getDefaultArgs([]));

	$sep = \DIRECTORY_SEPARATOR;
	$output = \file_get_contents(Components::getProjectPaths('srcDestination', "AdminMenus{$sep}AdminReusableBlocksMenu.php"));

	expect($output)
		->toContain(
			'class AdminReusableBlocksMenu',
			'Reusable Blocks',
			'Reusable Blocks',
			'edit_posts',
			'dashicons-admin-table',
		)
		->not->toContain(
			'class AdminReusableBlocksMenuExample',
			'%title%',
			'%menu_title%',
			'%capability%',
			'%menu_icon%',
			'%menu_position%',
		);
});

test('__invoke will will correctly copy example class with custom args', function () {
	$mock = $this->mock;
	$mock([], [
		'title' => 'Reusable Blocks Test',
		'menu_title' => 'Reusable Blocks Test',
		'capability' => 'edit_posts_test',
		'menu_icon' => 'dashicons-admin-table-test',
		'menu_position' => 5,
	]);

	$sep = \DIRECTORY_SEPARATOR;
	$output = \file_get_contents(Components::getProjectPaths('srcDestination', "AdminMenus{$sep}AdminReusableBlocksMenu.php"));

	expect($output)
		->toContain(
			'class AdminReusableBlocksMenu',
			'Reusable Blocks Test',
			'Reusable Blocks Test',
			'edit_posts_test',
			'dashicons-admin-table-test',
		)
		->not->toContain(
			'class AdminReusableBlocksMenuExample',
			'%title%',
			'%menu_title%',
			'%capability%',
			'%menu_icon%',
			'%menu_position%',
		);
});
