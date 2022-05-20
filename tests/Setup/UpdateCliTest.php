<?php

namespace Tests\Unit\Setup;

use EightshiftLibs\Setup\UpdateCli;
use Exception;

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
	deleteCliOutput();
});


test('Update CLI command will correctly throw an exception if setup.json does not exist or has the wrong filename', function () {
	$update = $this->update;
	$update([], []);
})->throws(Exception::class);

test('Update CLI documentation is correct', function () {
	expect($this->update->getDoc())->toBeArray();
});
