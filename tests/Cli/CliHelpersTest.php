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
	$output = \dirname(__FILE__, 3) . '/cliOutput';

	deleteCliOutput($output);

	putenv('ERROR_HAPPENED');
	putenv('INIT_CALLED');
	putenv('COMMAND_ADDED');

	Monkey\tearDown();
});

test('Return correct case - camelCase to kebab-case', function ($input, $output) {
	$case = CliHelpers::camelCaseToKebabCase($input);

	$this->assertIsString($case);
	$this->assertSame($case, $output);
})->with('camelToKebabCaseCheckCorrect');

test('Return wrong case - camelCase to kebab-case', function ($input, $output) {
	$case = CliHelpers::camelCaseToKebabCase($input);

	$this->assertIsString($case);
	$this->assertNotSame($case, $output);
})->with('camelToKebabCaseCheckWrong');

test('Return correct case - kebab-case to camelCase', function ($input, $output) {
	$case = CliHelpers::kebabToCamelCase($input);

	$this->assertIsString($case);
	$this->assertSame($case, $output);
})->with('kebabToCamelCaseCheckCorrect');

test('Return wrong case - kebab-case to camelCase', function ($input, $output) {
	$case = CliHelpers::kebabToCamelCase($input);

	$this->assertIsString($case);
	$this->assertNotSame($case, $output);
})->with('kebabToCamelCaseCheckWrong');

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

