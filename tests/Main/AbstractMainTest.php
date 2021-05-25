<?php

namespace Tests\Unit\Main;

use EightshiftLibs\Main\AbstractMain;
use EightshiftLibs\Main\MainCli;

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

$this->main = new MainCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	$output = dirname(__FILE__, 3) . '/cliOutput';

	deleteCliOutput($output);
});

test('Abstract main will instantiate services', function () {
	class MainTest extends AbstractMain {

		public function register(): void
		{
		}
	};

	$loader = require dirname(__DIR__, 2). '/vendor/autoload.php';

	$mainClass = new MainTest($loader->getPrefixesPsr4(), 'Tests\data\src');

	$mainClass->registerServices();
	$container = $mainClass->buildDiContainer();

	$this->assertIsObject($container);
	$this->assertSame('DI\Container', get_class($container));
});
