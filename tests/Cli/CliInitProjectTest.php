<?php

namespace Tests\Unit\Cli;

use Brain\Monkey;
use EightshiftLibs\Cli\CliInitProject;

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
		->andReturnUsing(function ($message) {
			putenv("SUCCESS={$message}");
		});

	$wpCliMock
		->shouldReceive('error')
		->andReturnArg(0);

	$wpCliMock
		->shouldReceive('log')
		->andReturnArg(0);

	$wpCliMock
		->shouldReceive('runcommand')
		->andReturn(putenv("INIT_CALLED=true"));

	$this->cliInitProject = new CliInitProject('setup_project');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	deleteCliOutput();

	putenv('SHELL_CALLED');
	putenv('SUCCESS');
	putenv('INIT_CALLED');

	Monkey\tearDown();
});


test('Initializing the project command returns correct command name', function () {
	$commandName = $this->cliInitProject->getCommandName();

	$this->assertIsString($commandName);
	$this->assertSame('project', $commandName);
});


test('CliInitProject CLI documentation is correct', function () {
	expect($this->cliInitProject->getDoc())->toBeArray();
});


test('InitProject CLI command will correctly copy the project classes', function () {
	$configProject = $this->cliInitProject;
	$configProject([], []);

	$this->assertSame('true', \getenv('INIT_CALLED'));
	$this->assertSame('All commands are finished.', \getenv('SUCCESS'));
});


test('InitProject CLI command runs in case WP is not installed', function () {
	redefine('shell_exec', always(true));
	redefine('function_exists', always(false));

	$configProject = $this->cliInitProject;
	$configProject([], []);

	$this->assertSame('true', \getenv('INIT_CALLED'));
});
