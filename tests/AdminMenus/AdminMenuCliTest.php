<?php

namespace Tests\Unit\CustomPostType;

use EightshiftLibs\AdminMenus\AdminMenuCli;
use EightshiftLibs\Cli\ParentGroups\CliCreate;
use EightshiftLibs\Helpers\Components;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->mock = new AdminMenuCli('boilerplate');
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
		->toEqual('admin_menu');
});

//---------------------------------------------------------------------------------//

test('getDefaultArgs will return correct array', function () {
	expect($this->mock->getDefaultArgs())
		->toBeArray()
		->toMatchArray([
			'title' => 'Admin Title',
			'menu_title' => 'Admin Menu Title',
			'capability' => 'edit_posts',
			'menu_slug' => 'example-menu-slug',
			'menu_icon' => 'dashicons-admin-generic',
			'menu_position' => 100,
		]);
});

//---------------------------------------------------------------------------------//

test('getDoc will return correct array', function () {
	$docs = $this->mock->getDoc();

	expect($docs)
		->toBeArray()
		->toHaveKeys(['shortdesc', 'synopsis', 'longdesc'])
		->and(count($docs['synopsis']))->toEqual(6)
		->and($docs['synopsis'][0]['name'])->toEqual('title')
		->and($docs['synopsis'][1]['name'])->toEqual('menu_title')
		->and($docs['synopsis'][2]['name'])->toEqual('capability')
		->and($docs['synopsis'][3]['name'])->toEqual('menu_slug')
		->and($docs['synopsis'][4]['name'])->toEqual('menu_icon')
		->and($docs['synopsis'][5]['name'])->toEqual('menu_position');
});

//---------------------------------------------------------------------------------//

test('__invoke will will correctly copy example class with default args', function () {
	$mock = $this->mock;
	$mock([], $this->mock->getDefaultArgs([]));

	$sep = \DIRECTORY_SEPARATOR;
	$output = \file_get_contents(Components::getProjectPaths('srcDestination', "AdminMenus{$sep}ExampleMenuSlugAdminMenu.php"));

	expect($output)
		->toContain(
			'class ExampleMenuSlugAdminMenu',
			'Admin Title',
			'Admin Menu Title',
			'edit_posts',
			'example-menu-slug',
			'dashicons-admin-generic',
		)
		->not->toContain(
			'class AdminMenuExample',
			'%title%',
			'%menu_title%',
			'%capability%',
			'%menu_slug%',
			'%menu_icon%',
		);
});

test('__invoke will will correctly copy example class with custom args', function () {
	$mock = $this->mock;
	$mock([], [
		'title' => 'Admin Title Test',
		'menu_title' => 'Admin Menu Title Test',
		'capability' => 'edit_posts_test',
		'menu_slug' => 'example-menu-slug-test',
		'menu_icon' => 'dashicons-admin-generic-test',
		'menu_position' => 200,
	]);

	$sep = \DIRECTORY_SEPARATOR;
	$output = \file_get_contents(Components::getProjectPaths('srcDestination', "AdminMenus{$sep}ExampleMenuSlugTestAdminMenu.php"));

	expect($output)
		->toContain(
			'class ExampleMenuSlugTestAdminMenu',
			'Admin Title Test',
			'Admin Menu Title Test',
			'edit_posts_test',
			'example-menu-slug-test',
			'dashicons-admin-generic-test',
		)
		->not->toContain(
			'class AdminMenuExample',
			'%title%',
			'%menu_title%',
			'%capability%',
			'%menu_slug%',
			'%menu_icon%',
		);
});
