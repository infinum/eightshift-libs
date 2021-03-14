<?php

namespace Tests\Unit\Cli;

use Brain\Monkey;
use Brain\Monkey\Functions;
use EightshiftLibs\Cli\Cli;

use function Patchwork\{redefine, always};

use function Tests\deleteCliOutput;

/**
 * Mock before tests.
 */
beforeEach(function () {
	Monkey\setUp();

	$wpCliMock = \Mockery::mock('alias:WP_CLI');

	$wpCliMock
		->shouldReceive('success')
		->andReturnArg(0);

	$wpCliMock
		->shouldReceive('error')
		->andReturnArg(0);

	$wpCliMock
		->shouldReceive('log')
		->andReturnArg(0);

	$wpCliMock
		->shouldReceive('runcommand')
		->andReturn(putenv("INIT_CALLED=true"));

	$this->cli = new Cli();
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	$output = dirname(__FILE__, 3) . '/cliOutput';

	deleteCliOutput($output);

	Monkey\tearDown();
});


test('Cli getDevelopClasses return correct class list', function () {
	$developClasses = $this->cli->getDevelopClasses();

	$this->assertIsArray($developClasses);
});


test('Cli getPublicClasses return correct class list', function () {
	$publicClasses = $this->cli->getPublicClasses();

	$this->assertIsArray($publicClasses);
});
