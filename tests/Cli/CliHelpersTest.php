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
	$output = dirname(__FILE__, 3) . '/cliOutput';

	deleteCliOutput($output);

	putenv('ERROR_HAPPENED');
	putenv('INIT_CALLED');
	putenv('COMMAND_ADDED');

	Monkey\tearDown();
});

test('Return correct case', function($input, $output) {
	$case = CliHelpers::camelCaseToKebabCase($input);

	$this->assertIsString($case);
	$this->assertSame($case, $output);
})->with('caseCheckCorrect');

test('Return wrong case', function($input, $output) {
	$case = CliHelpers::camelCaseToKebabCase($input);

	$this->assertIsString($case);
	$this->assertNotSame($case, $output);
})->with('caseCheckWrong');
