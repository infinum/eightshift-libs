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
	deleteCliOutput();
});

test('Abstract main will instantiate services', function () {
	class MainTest extends AbstractMain {

		public function register(): void
		{
		}
	};

	$loader = require \dirname(__DIR__, 3) . '/vendor/autoload.php';

	$mainClass = new MainTest($loader->getPrefixesPsr4(), 'Tests\data\src');

	$mainClass->registerServices();
	$container = $mainClass->buildDiContainer();

	$this->assertIsObject($container);
	$this->assertSame('DI\Container', \get_class($container));
});

test('Caching compiled services works', function() {
	define('WP_ENVIRONMENT_TYPE', 'staging');

	class MainCompiledTest extends AbstractMain {

		public function register(): void
		{
			\add_action('after_setup_theme', [$this, 'registerServices']);
		}
	};

	$loader = require \dirname(__DIR__, 3) . '/vendor/autoload.php';

	$mainClass = new MainCompiledTest($loader->getPrefixesPsr4(), 'Tests\data\src');

	$mainClass->registerServices();
	$mainClass->buildDiContainer();

	// Check if compiled container was created.
	$this->assertFileExists(\dirname(__FILE__, 4) . '/src/Main/Cache/TestsCompiledContainer.php', 'Compiled container was not created');
	// Delete it if it has been created. Because it will be created in the code, and we do not want to commit it.
	unlink(\dirname(__FILE__, 4) . '/src/Main/Cache/TestsCompiledContainer.php');
	rmdir(\dirname(__FILE__, 4) . '/src/Main/Cache');
});
