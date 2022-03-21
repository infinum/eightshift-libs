<?php

namespace Tests\Unit\Cli;

use Brain\Monkey;
use EightshiftLibs\Cli\CliShowAll;

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
		->shouldReceive('colorize')
		->andReturnUsing(function ($message) {
			putenv("COLORIZE={$message}");
		});

	$wpCliMock
		->shouldReceive('log')
		->andReturn('log');

	$this->cliShowAll = new CliShowAll('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	putenv('SUCCESS');
	putenv('COLORIZE');
	putenv('RUNCOMMAND');

	Monkey\tearDown();
});


test('CliShowAll works', function () {
	$cliShowAll = $this->cliShowAll;
	$cliShowAll([], []);

	$this->assertSame('All commands are outputted.', \getenv('SUCCESS'));
	$this->assertSame('%mCommands for project setup:%n', \getenv('COLORIZE')); // Last colorize command.
});


test('CliShowAll CLI documentation is correct', function () {
	$documentation = $this->cliShowAll->getDoc();

	$key = 'shortdesc';

	$this->assertIsArray($documentation);
	$this->assertArrayHasKey($key, $documentation);
	$this->assertSame('DEVELOP - Used to show all commands.', $documentation[$key]);
});
