<?php

namespace Tests\Unit\GitIgnore;

use EightshiftLibs\Cli\ParentGroups\CliProject;
use EightshiftLibs\GitIgnore\GitIgnoreCli;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;
use function Tests\getCliOutputFile;

beforeEach(function () {
	setBeforeEach();

	$this->mock = new GitIgnoreCli('boilerplate');
});

afterEach(function () {
	setAfterEach();

	unset($this->mock);
});

//---------------------------------------------------------------------------------//

test('getCommandParentName will return correct value', function () {
	expect($this->mock->getCommandParentName())
		->toBeString()
		->toEqual(CliProject::COMMAND_NAME);
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
		->toMatchArray([
			'root' => '../../../',
		]);
});

//---------------------------------------------------------------------------------//

test('getDoc will return correct array', function () {
	$docs = $this->mock->getDoc();

	expect($docs)
		->toBeArray()
		->toHaveKeys(['shortdesc', 'synopsis', 'longdesc'])
		->and(count($docs['synopsis']))->toEqual(1)
		->and($docs['synopsis'][0]['name'])->toEqual('root');
});

//---------------------------------------------------------------------------------//

test('__invoke will will correctly copy example class with default args', function () {
	$mock = $this->mock;
	$mock([], [
		'root' => './'
	]);

	expect(getCliOutputFile('.gitignore'))
		->toContain(
			'wp-admin',
			'/index.php',
			'wp-content/*',
			'wp-content/themes/twenty*/',
		);
});
