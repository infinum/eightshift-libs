<?php

namespace Tests\Unit\WpCli;

use EightshiftBoilerplate\WpCli\WpCliExample;
use EightshiftLibs\WpCli\WpCli;

use function Tests\deleteCliOutput;
use function Tests\mock;

/**
 * Mock before tests.
 */
beforeEach(function () {
	$wpCliMock = mock('alias:WP_CLI');

	$wpCliMock
		->shouldReceive('success')
		->andReturnArg(0);

	$wpCliMock
		->shouldReceive('error')
		->andReturnArg(0);

	$this->customCommand = new WpCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	deleteCliOutput();
});


test('Custom command CLI command will correctly copy the Custom command class with defaults', function () {
	$customCommand = $this->customCommand;
	$customCommand([], $customCommand->getDevelopArgs([]));

	// Check the output dir if the generated method is correctly generated.

	$customCommand = \file_get_contents(\dirname(__FILE__, 4) . '/cliOutput/src/CliCommands/TestCustomCommand.php');

	$this->assertStringContainsString('class TestWpCli implements ServiceCliInterface', $customCommand);
	$this->assertStringContainsString('function register', $customCommand);
	$this->assertStringContainsString('function registerCommand', $customCommand);
	$this->assertStringContainsString('function getDocs', $customCommand);
	$this->assertStringContainsString('function __invoke', $customCommand);
});


test('Custom command CLI documentation is correct', function () {
	$customCommand = $this->customCommand;

	$documentation = $customCommand->getDoc();

	$descKey = 'shortdesc';
	$synopsisKey = 'synopsis';

	$this->assertIsArray($documentation);
	$this->assertArrayHasKey($descKey, $documentation);
	$this->assertArrayHasKey($synopsisKey, $documentation);
	$this->assertIsArray($documentation[$synopsisKey]);
	$this->assertSame('Generates custom WPCLI command in your project.', $documentation[$descKey]);

	$this->assertSame('assoc', $documentation[$synopsisKey][0]['type']);
	$this->assertSame('command_name', $documentation[$synopsisKey][0]['name']);
	$this->assertSame('The name of cli command name. Example: command_name.', $documentation[$synopsisKey][0]['description']);
	$this->assertSame(false, $documentation[$synopsisKey][0]['optional']);
});