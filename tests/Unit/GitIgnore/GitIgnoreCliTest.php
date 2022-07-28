<?php

namespace Tests\Unit\GitIgnore;

use EightshiftLibs\Cli\ParentGroups\CliCreate;
use EightshiftLibs\GitIgnore\GitIgnoreCli;
use EightshiftLibs\Helpers\Components;

beforeEach(function () {
	$this->mock = new GitIgnoreCli('boilerplate');
});

afterEach(function () {
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
		->toEqual('gitignore');
});

//---------------------------------------------------------------------------------//

test('getDefaultArgs will return correct array', function () {
	expect($this->mock->getDefaultArgs())
		->toBeArray()
		->toHaveKeys(['path']);
});

//---------------------------------------------------------------------------------//

test('getDoc will return correct array', function () {
	$docs = $this->mock->getDoc();

	expect($docs)
		->toBeArray()
		->toHaveKeys(['shortdesc', 'synopsis', 'longdesc'])
		->and(count($docs['synopsis']))->toEqual(1)
		->and($docs['synopsis'][0]['name'])->toEqual('path');
});

//---------------------------------------------------------------------------------//

test('__invoke will will correctly copy example class with default args', function () {
	$mock = $this->mock;
	$mock([], [
		'path' => Components::getProjectPaths('cliOutput'),
	]);

	$output = \file_get_contents(Components::getProjectPaths('cliOutput', '.gitignore'));

	expect($output)
		->toContain(
			'wp-admin',
			'/index.php',
			'wp-content/*',
			'wp-content/themes/twenty*/',
		);
});
