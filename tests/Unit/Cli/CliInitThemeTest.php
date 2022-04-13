<?php

namespace Tests\Unit\Cli;

use Brain\Monkey;
use Brain\Monkey\Functions;
use EightshiftLibs\Cli\CliInitTheme;

use function Patchwork\{redefine, always};
use function Tests\deleteCliOutput;
use function Tests\mock;

/**
 * Mock before tests.
 */
beforeEach(function () {
	Monkey\setUp();

	$wpCliMock = mock('alias:WP_CLI');

	$wpCliMock
		->shouldReceive('success')
		->andReturnArg(0);

	$wpCliMock
		->shouldReceive('error')
		->andReturnArg(0);

	$wpCliMock
		->shouldReceive('log')
		->andReturnArg(0);

	$wpCliMock
		->shouldReceive('runcommand')
		->andReturn(putenv("THEME_INIT_CALLED=true"));

	$this->cliInitTheme = new CliInitTheme('setup_theme');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	deleteCliOutput();

	Monkey\tearDown();
});


test('Initializing the project command returns correct command name', function () {
	$commandName = $this->cliInitTheme->getCommandName();

	$this->assertIsString($commandName);
	$this->assertSame(($this->cliInitTheme)::COMMAND_NAME, $commandName);
});


test('CliInitTheme CLI documentation is correct', function () {
	$documentation = $this->cliInitTheme->getDoc();

	$key = 'shortdesc';

	$this->assertIsArray($documentation);
	$this->assertArrayHasKey($key, $documentation);
	$this->assertSame('Generates initial setup for WordPress theme project.', $documentation[$key]);
});


test('InitTheme CLI command will correctly copy the project classes', function () {
	Functions\when('shell_exec')->returnArg();

	$configProject = $this->cliInitTheme;
	$configProject([], []);

	$this->assertSame('true', \getenv('THEME_INIT_CALLED'));
});


test('InitTheme CLI command runs in case WP is not installed', function () {
	redefine('shell_exec', always(true));
	redefine('function_exists', always(false));

	$configProject = $this->cliInitTheme;
	$configProject([], []);

	$this->assertSame('true', \getenv('THEME_INIT_CALLED'));
});
