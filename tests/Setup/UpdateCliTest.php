<?php

namespace Tests\Unit\Setup;

use EightshiftLibs\Setup\UpdateCli;
use Throwable;
use WP_CLI\ExitException;

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

	$wpCliMock
		->shouldReceive('runcommand')
		->andReturnArg(0);

	$wpCliMock
		->shouldReceive('log')
		->andReturnArg(0);

	$this->update = new UpdateCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	$output = dirname(__FILE__, 3) . '/cliOutput';

	deleteCliOutput($output);
});

test('Update CLI command will correctly run the update with defaults', function () {
	// Create cliOutput folder ...
	$outputDir = dirname(__FILE__, 3) . '/cliOutput';

	if (!is_dir($outputDir)) {
		mkdir($outputDir, 0755, true);
	}

	// ... so you can copy the example setup.json inside
	copy(dirname(__FILE__, 3) . '/src/Setup/setup.json', dirname(__FILE__, 3) . '/cliOutput/setup.json');

	// Check if an exception occured
	$exceptionOccured = false;

	$update = $this->update;
	$update([], []);
});

test('Update CLI command will correctly throw an exception if setup.json does not exist or has the wrong filename', function () {
	$update = $this->update;
	$update([], []);
})->throws(\Exception::class);

test('Update CLI documentation is correct', function () {
	$update = $this->update;

	$documentation = $update->getDoc();

	$key = 'shortdesc';

	$this->assertIsArray($documentation);
	$this->assertArrayHasKey($key, $documentation);
	$this->assertArrayHasKey('synopsis', $documentation);
	$this->assertEquals('Run project update with details stored in setup.json file.', $documentation[$key]);
});
