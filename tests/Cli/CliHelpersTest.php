<?php

namespace Tests\Unit\Cli;

use EightshiftLibs\Cli\CliHelpers;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

/**
 * Mock before tests.
 */
beforeEach(function () {
	setBeforeEach();
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	setAfterEach();
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

