<?php

namespace Tests\Unit\Setup;

use EightshiftLibs\Setup\SetupCli;

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

	$this->setup = new SetupCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	deleteCliOutput();
});

test('Setup CLI command will correctly copy the Setup class with defaults', function () {
	$setup = $this->setup;
	$setup([], $setup->getDevelopArgs([]));

	// Check the output dir if the generated method is correctly generated.
	$generatedFile = \file_get_contents(\dirname(__FILE__, 3) . '/cliOutput/setup.json');
	$this->assertStringContainsString('twentytwentyone', $generatedFile);
	$this->assertStringNotContainsString('random string', $generatedFile);
});

test('Setup CLI command will correctly copy the Setup class with set parameters', function () {
	$setup = $this->setup;
	$setup([], [
		'root' => 'test',
	]);

	// Check the output dir if the generated method is correctly generated.
	$generatedFile = \file_get_contents(\dirname(__FILE__, 3) . '/cliOutput/test/setup.json');
	$this->assertStringContainsString('twentytwentyone', $generatedFile);
	$this->assertStringNotContainsString('random string', $generatedFile);
});

test('Setup CLI documentation is correct', function () {
	expect($this->setup->getDoc())->toBeArray();
});
