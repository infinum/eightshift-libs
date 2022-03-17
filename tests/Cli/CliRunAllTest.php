<?php

namespace Tests\Unit\Cli;

use Brain\Monkey;

use EightshiftLibs\Cli\CliRunAll;

use function Tests\mock;

/**
 * Mock before tests.
 */
beforeEach(function () {
	Monkey\setUp();

	$wpCliMock = mock('alias:WP_CLI');

	$wpCliMock
		->shouldReceive('success')
		->andReturnUsing(function ($message) {
			putenv("SUCCESS={$message}");
		});

	$wpCliMock
		->shouldReceive('runcommand')
		->andReturnUsing(function ($message) {
			putenv("RUNCOMMAND={$message}");
		});

	$wpCliMock
		->shouldReceive('log')
		->andReturn('log');

	$this->cliRunAll = new CliRunAll('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	putenv('SUCCESS');
	putenv('RUNCOMMAND');

	Monkey\tearDown();
});


test('CliRunAll works', function () {
	$cliRunAll = $this->cliRunAll;
	$cliRunAll([], []);

	$this->assertSame('All commands are finished.', \getenv('SUCCESS'));
});


test('CliRunAll CLI documentation is correct', function () {
	$documentation = $this->cliRunAll->getDoc();

	$key = 'shortdesc';

	$this->assertIsArray($documentation);
	$this->assertArrayHasKey($key, $documentation);
	$this->assertSame('DEVELOP - Used to run all commands.', $documentation[$key]);
});
