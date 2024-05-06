<?php

namespace Tests\Unit\Readme;

use EightshiftLibs\Cli\ParentGroups\CliCreate;
use EightshiftLibs\Helpers\Helpers;
use EightshiftLibs\Readme\ReadmeCli;

use function Tests\getMockArgs;

beforeEach(function () {
	$this->mock = new ReadmeCli('boilerplate');
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
		->toEqual('readme');
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
	$mock([], getMockArgs([
		'path' => Helpers::getProjectPaths('srcDestination'),
	]));

	$output = \file_get_contents(Helpers::getProjectPaths('srcDestination', 'README.md'));

	expect($output)
		->toContain(
			'This is the official repository of the {Project name}.',
			'Installation',
			'Once you clone this repository, you\'ll need to build it:',
			'Development',
			'Environments',
		);
});
