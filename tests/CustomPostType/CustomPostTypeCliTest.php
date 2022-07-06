<?php

namespace Tests\Unit\CustomPostType;

use EightshiftLibs\Cli\ParentGroups\CliCreate;
use EightshiftLibs\CustomPostType\PostTypeCli;
use EightshiftLibs\Helpers\Components;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->mock = new PostTypeCli('boilerplate');
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
		->toEqual('post-type');
});

//---------------------------------------------------------------------------------//

test('getDefaultArgs will return correct array', function () {
	expect($this->mock->getDefaultArgs())
		->toBeArray()
		->toMatchArray([
			'label' => 'Product',
			'plural_label' => 'Products',
			'slug' => 'product',
			'rewrite_url' => 'product',
			'rest_endpoint_slug' => 'products',
			'capability' => 'post',
			'menu_position' => 20,
			'menu_icon' => 'admin-settings',
		]);
});

//---------------------------------------------------------------------------------//

test('getDoc will return correct array', function () {
	$docs = $this->mock->getDoc();

	expect($docs)
		->toBeArray()
		->toHaveKeys(['shortdesc', 'synopsis', 'longdesc'])
		->and(count($docs['synopsis']))->toEqual(8)
		->and($docs['synopsis'][0]['name'])->toEqual('label')
		->and($docs['synopsis'][1]['name'])->toEqual('plural_label')
		->and($docs['synopsis'][2]['name'])->toEqual('slug')
		->and($docs['synopsis'][3]['name'])->toEqual('rewrite_url')
		->and($docs['synopsis'][4]['name'])->toEqual('rest_endpoint_slug')
		->and($docs['synopsis'][5]['name'])->toEqual('capability')
		->and($docs['synopsis'][6]['name'])->toEqual('menu_position')
		->and($docs['synopsis'][7]['name'])->toEqual('menu_icon');
});

//---------------------------------------------------------------------------------//

test('__invoke will will correctly copy example class with default args', function () {
	$mock = $this->mock;
	$mock([], $this->mock->getDefaultArgs());

	$sep = \DIRECTORY_SEPARATOR;

	$output = file_get_contents(Components::getProjectPaths('srcDestination', "CustomPostType{$sep}ProductPostType.php"));

	expect($output)
		->toContain(
			'class ProductPostType',
			'Product',
			'Products',
			'product',
			'product',
			'products',
			'post',
			'20',
			'admin-settings',
		)
		->not->toContain(
			'class PostTypeExample',
			'%label%',
			'%plural_label%',
			'%slug%',
			'%rewrite_url%',
			'%rest_endpoint_slug%',
			'%capability%',
			'%menu_position%',
			'%menu_icon%',
		);
});

test('__invoke will will correctly copy example class with custom args', function () {
	$mock = $this->mock;
	$mock([], [
		'label' => 'Test',
		'plural_label' => 'Tests',
		'slug' => 'test',
		'rewrite_url' => 'test',
		'rest_endpoint_slug' => 'tests',
		'capability' => 'product',
		'menu_position' => 40,
		'menu_icon' => 'admin-panel',
	]);

	$sep = \DIRECTORY_SEPARATOR;
	$output = file_get_contents(Components::getProjectPaths('srcDestination', "CustomPostType{$sep}TestPostType.php"));

	expect($output)
		->toContain(
			'class TestPostType',
			'Test',
			'Tests',
			'test',
			'test',
			'tests',
			'product',
			'40',
			'admin-panel',
		)
		->not->toContain(
			'class PostTypeExample',
			'%label%',
			'%plural_label%',
			'%slug%',
			'%rewrite_url%',
			'%rest_endpoint_slug%',
			'%capability%',
			'%menu_position%',
			'%menu_icon%',
		);
});
