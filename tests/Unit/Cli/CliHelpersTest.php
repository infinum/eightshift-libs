<?php

namespace Tests\Unit\Cli;

use Brain\Monkey;
use EightshiftLibs\Cli\CliHelpers;

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
		->andReturnUsing(function ($message) {
			putenv("ERROR_HAPPENED={$message}");
		});

	$wpCliMock
		->shouldReceive('log')
		->andReturnArg(0);

	$wpCliMock
		->shouldReceive('runcommand')
		->andReturn(putenv("INIT_CALLED=true"));

	$wpCliMock
		->shouldReceive('add_command')
		->andReturn(putenv("COMMAND_ADDED=true"));
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	deleteCliOutput();

	putenv('ERROR_HAPPENED');
	putenv('INIT_CALLED');
	putenv('COMMAND_ADDED');

	Monkey\tearDown();
});

test('Return correct name - getGithubPluginName', function ($input, $output) {
	$case = CliHelpers::getGithubPluginName($input);

	$this->assertIsString($case);
	$this->assertSame($case, $output);
})->with('getGithubPluginNameCorrect');

test('Return wrong name - getGithubPluginName', function ($input, $output) {
	$case = CliHelpers::getGithubPluginName($input);

	$this->assertIsString($case);
	$this->assertNotSame($case, $output);
})->with('getGithubPluginNameWrong');

