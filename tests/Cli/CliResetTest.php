<?php

namespace Tests\Unit\Cli;

use Brain\Monkey;
use Brain\Monkey\Functions;
use EightshiftLibs\Cli\CliReset;

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

	$this->cliReset = new CliReset('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	putenv('SYSTEM_CALLED');
	putenv('SUCCESS');

	Monkey\tearDown();
});


test('CliReset works', function () {
	Functions\when('system')->alias(function ($args) {
		putenv("SYSTEM_CALLED={$args}");
	});

	$cliReset = $this->cliReset;
	$cliReset([], []);

	$this->assertSame('Output directory successfully removed.', getenv('SUCCESS'));
	$this->assertStringContainsString('rm -rf', getenv('SYSTEM_CALLED'));
});


test('CliReset CLI documentation is correct', function () {
	$documentation = $this->cliReset->getDoc();

	$key = 'shortdesc';

	$this->assertIsArray($documentation);
	$this->assertArrayHasKey($key, $documentation);
	$this->assertEquals('DEVELOP - Used to reset and remove all outputs.', $documentation[$key]);
});
