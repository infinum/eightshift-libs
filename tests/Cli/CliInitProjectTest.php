<?php

namespace Tests\Unit\Cli;

use EightshiftLibs\Cli\CliInitProject;
use Brain\Monkey;
use Brain\Monkey\Functions;
use function Tests\deleteCliOutput;

/**
 * Mock before tests.
 */
beforeEach(function () {
	Monkey\setUp();

	$wpCliMock = \Mockery::mock('alias:WP_CLI');

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
		->andReturn(putenv("INIT_CALLED=true"));

	$this->cliInitProject = new CliInitProject('');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	$output = dirname(__FILE__, 3) . '/cliOutput';

	deleteCliOutput($output);

	Monkey\tearDown();
});


test('Initializing the project command returns correct command name', function () {
	$commandName = $this->cliInitProject->getCommandName();

	$this->assertIsString($commandName);
	$this->assertSame(($this->cliInitProject)::COMMAND_NAME, $commandName);
});


test('CliInitProject CLI documentation is correct', function () {
	$documentation = $this->cliInitProject->getDoc();

	$key = 'shortdesc';

	$this->assertIsArray($documentation);
	$this->assertArrayHasKey($key, $documentation);
	$this->assertEquals('Generates initial setup for WordPress theme project with all files to run a client project, for example: gitignore file for the full WordPress project, continuous integration exclude files, etc.', $documentation[$key]);
});


test('InitProject CLI command will correctly copy the project classes', function () {
	Functions\when('shell_exec')->returnArg();

	$configProject = $this->cliInitProject;
	$configProject([], []);

	$this->assertSame('true', getenv('INIT_CALLED'));
});
