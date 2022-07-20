<?php

namespace Tests\Unit\Main;

use EightshiftLibs\Helpers\Components;
use EightshiftLibs\Main\AbstractMain;
use EightshiftLibs\Main\MainCli;

beforeEach(function () {
	$this->mock = new MainCli('boilerplate');
});

afterEach(function () {
	unset($this->mock);
});

test('Abstract main will instantiate services', function () {
	class MainTest extends AbstractMain {

		public function register(): void
		{
		}
	};

	$loader = require Components::getProjectPaths('libs', 'vendor/autoload.php');

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

	$loader = require Components::getProjectPaths('libs', 'vendor/autoload.php');

	$mainClass = new MainCompiledTest($loader->getPrefixesPsr4(), 'Tests\data\src');

	$mainClass->registerServices();
	$mainClass->buildDiContainer();

	// Check if compiled container was created.
	$this->assertFileExists(Components::getProjectPaths('libs', 'src/Main/Cache/TestsCompiledContainer.php'), 'Compiled container was not created');
	// Delete it if it has been created. Because it will be created in the code, and we do not want to commit it.
	unlink(Components::getProjectPaths('libs', 'src/Main/Cache/TestsCompiledContainer.php'));
	rmdir(Components::getProjectPaths('libs', 'src/Main/Cache'));
});
