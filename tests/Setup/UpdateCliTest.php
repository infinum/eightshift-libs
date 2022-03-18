<?php

namespace Tests\Unit\Setup;

use EightshiftLibs\Setup\UpdateCli;

use function Brain\Monkey\Functions\when;
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

	$wpCliMock
		->shouldReceive('runcommand')
		->andReturnUsing(function ($cmd) {
			putenv("COMMAND={$cmd}");
		});

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
	$this->assertSame('Run project update with details stored in setup.json file.', $documentation[$key]);
});
