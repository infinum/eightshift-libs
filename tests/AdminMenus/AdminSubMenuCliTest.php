<?php

namespace Tests\Unit\CustomPostType;

use EightshiftLibs\AdminMenus\AdminSubMenuCli;
use EightshiftLibs\Cli\ParentGroups\CliCreate;
use EightshiftLibs\Helpers\Components;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->mock = new AdminSubMenuCli('boilerplate');
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
		->toEqual('admin-sub-menu');
});

//---------------------------------------------------------------------------------//

test('getDefaultArgs will return correct array', function () {
	expect($this->mock->getDefaultArgs())
		->toBeArray()
		->toMatchArray([
			'parent_slug' => 'example-parent-slug',
			'title' => 'Admin Title',
			'menu_title' => 'Admin Sub Menu Title',
			'capability' => 'edit_posts',
			'menu_slug' => 'example-menu-slug',
		]);
});

//---------------------------------------------------------------------------------//

test('getDoc will return correct array', function () {
	$docs = $this->mock->getDoc();

	expect($docs)
		->toBeArray()
		->toHaveKeys(['shortdesc', 'synopsis', 'longdesc'])
		->and(count($docs['synopsis']))->toEqual(5)
		->and($docs['synopsis'][0]['name'])->toEqual('parent_slug')
		->and($docs['synopsis'][1]['name'])->toEqual('title')
		->and($docs['synopsis'][2]['name'])->toEqual('menu_title')
		->and($docs['synopsis'][3]['name'])->toEqual('capability')
		->and($docs['synopsis'][4]['name'])->toEqual('menu_slug');
});

//---------------------------------------------------------------------------------//

test('__invoke will will correctly copy example class with default args', function () {
	$mock = $this->mock;
	$mock([], $this->mock->getDefaultArgs([]));

	$sep = \DIRECTORY_SEPARATOR;
	$output = \file_get_contents(Components::getProjectPaths('srcDestination', "AdminMenus{$sep}ExampleMenuSlugAdminSubMenu.php"));

	expect($output)
		->toContain(
			'class ExampleMenuSlugAdminSubMenu',
			'example-parent-slug',
			'Admin Title',
			'Admin Sub Menu Title',
			'edit_posts',
			'example-menu-slug',
		)
		->not->toContain(
			'class AdminSubMenuExample',
			'%parent_slug%',
			'%title%',
			'%menu_title%',
			'%capability%',
			'%menu_slug%',
		);
});

test('__invoke will will correctly copy example class with custom args', function () {
	$mock = $this->mock;
	$mock([], [
		'parent_slug' => 'example-parent-slug-test',
		'title' => 'Admin Title Test',
		'menu_title' => 'Admin Sub Menu Title Test',
		'capability' => 'edit_posts_test',
		'menu_slug' => 'example-menu-slug-test',
	]);

	$sep = \DIRECTORY_SEPARATOR;
	$output = \file_get_contents(Components::getProjectPaths('srcDestination', "AdminMenus{$sep}ExampleMenuSlugTestAdminSubMenu.php"));

	expect($output)
		->toContain(
			'class ExampleMenuSlugTestAdminSubMenu',
			'example-parent-slug-test',
			'Admin Title Test',
			'Admin Sub Menu Title Test',
			'edit_posts_test',
			'example-menu-slug-test',
		)
		->not->toContain(
			'class AdminSubMenuExample',
			'%parent_slug%',
			'%title%',
			'%menu_title%',
			'%capability%',
			'%menu_slug%',
		);
});
