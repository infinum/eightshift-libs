<?php

namespace Tests\Unit\Cli;

use EightshiftLibs\Cli\CliInitProject;

use function Patchwork\{redefine, always};
use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->mock = new CliInitProject('boilerplate');
});

afterEach(function () {
	setAfterEach();

	unset($this->mock);
});

test('Initializing the project command returns correct command name', function () {
	$commandName = $this->mock->getCommandName();

	$this->assertIsString($commandName);
	$this->assertSame('project', $commandName);
});


test('CliInitProject CLI documentation is correct', function () {
	expect($this->mock->getDoc())->toBeArray();
});


test('InitProject CLI command will correctly copy the project classes', function () {
	$configProject = $this->mock;
	$configProject([], []);

	expect(\getenv('ES_CLI_SUCCESS_HAPPENED'))->toEqual('All commands are finished.');
});


test('InitProject CLI command runs in case WP is not installed', function () {
	redefine('shell_exec', always(true));
	redefine('function_exists', always(false));

	$configProject = $this->mock;
	$configProject([], []);

	expect(\getenv('ES_CLI_SUCCESS_HAPPENED'))->toEqual('All commands are finished.');
});
