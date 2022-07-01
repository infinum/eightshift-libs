<?php

namespace Tests\Unit\ConfigProject;

use EightshiftLibs\Cli\ParentGroups\CliProject;
use EightshiftLibs\ConfigProject\ConfigProjectCli;
use EightshiftLibs\Helpers\Components;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->mock = new ConfigProjectCli('boilerplate');
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
		->toEqual('config_project');
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

	$output = \file_get_contents(Components::getProjectPaths('cliOutput', "wp-config-project.php"));

	expect($output)
		->toContain(
			'WP_ENVIRONMENT_TYPE',
			'WP_POST_REVISIONS',
			'WP_DEBUG_DISPLAY',
		)
		->and(\getenv('ES_CLI_SUCCESS_HAPPENED'))->toContain('Sets up WordPress vars and included files.');
});
