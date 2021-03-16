<?php

namespace Tests\Unit\Setup;

use EightshiftLibs\Setup\SetupCli;

use function Tests\deleteCliOutput;

/**
 * Mock before tests.
 */
beforeEach(function () {
	$wpCliMock = \Mockery::mock('alias:WP_CLI');

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
	$output = dirname(__FILE__, 3) . '/cliOutput';

	deleteCliOutput($output);
});

test('Setup CLI command will correctly copy the Setup class with defaults', function () {
	$setup = $this->setup;
	$setup([], $setup->getDevelopArgs([]));

	// Check the output dir if the generated method is correctly generated.
	$generatedFile = file_get_contents(dirname(__FILE__, 3) . '/cliOutput/setup.json');
	$this->assertStringContainsString('twentytwentyone', $generatedFile);
	$this->assertStringNotContainsString('random string', $generatedFile);
});

test('Setup CLI command will correctly copy the Setup class with set parameters', function () {
	$setup = $this->setup;
	$setup([], [
		'root' => 'test',
	]);

	// Check the output dir if the generated method is correctly generated.
	$generatedFile = file_get_contents(dirname(__FILE__, 3) . '/cliOutput/test/setup.json');
	$this->assertStringContainsString('twentytwentyone', $generatedFile);
	$this->assertStringNotContainsString('random string', $generatedFile);
});

test('Setup CLI documentation is correct', function () {
	$setup = $this->setup;

	$documentation = $setup->getDoc();

	$key = 'shortdesc';

	$this->assertIsArray($documentation);
	$this->assertArrayHasKey($key, $documentation);
	$this->assertArrayHasKey('synopsis', $documentation);
	$this->assertEquals('Initialize Command for automatic project setup and update.', $documentation[$key]);
});
