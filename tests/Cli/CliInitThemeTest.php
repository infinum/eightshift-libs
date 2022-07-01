<?php

namespace Tests\Unit\Cli;

use Brain\Monkey\Functions;
use EightshiftLibs\Cli\CliInitTheme;

use function Patchwork\{redefine, always};
use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->mock = new CliInitTheme('boilerplate');
});

afterEach(function () {
	setAfterEach();

	unset($this->mock);
});

test('Initializing the project command returns correct command name', function () {
	$commandName = $this->mock->getCommandName();

	$this->assertIsString($commandName);
	$this->assertSame('theme', $commandName);
});


test('CliInitTheme CLI documentation is correct', function () {
	expect($this->mock->getDoc())->toBeArray();
});


test('InitTheme CLI command will correctly copy the project classes', function () {
	Functions\when('shell_exec')->returnArg();

	$configProject = $this->mock;
	$configProject([], []);

	$this->assertSame('boilerplate create blocks ', \getenv('ES_CLI_RUN_COMMAND_HAPPENED'));
});


test('InitTheme CLI command runs in case WP is not installed', function () {
	redefine('shell_exec', always(true));
	redefine('function_exists', always(false));

	$configProject = $this->mock;
	$configProject([], []);

	$this->assertSame('boilerplate create blocks ', \getenv('ES_CLI_RUN_COMMAND_HAPPENED'));
});
