<?php

namespace Tests\Unit\Enqueue\Admin;

use Brain\Monkey;
use Brain\Monkey\Functions;
use EightshiftBoilerplate\Enqueue\Admin\EnqueueAdminExample;
use EightshiftBoilerplate\Manifest\ManifestExample;
use EightshiftLibs\Enqueue\AbstractAssets;
use EightshiftLibs\Manifest\ManifestInterface;

use function Tests\mock;
use function Tests\setupMocks;

class EnqueueAdminExampleTest extends EnqueueAdminExample {

	public function __construct(ManifestInterface $manifest)
	{
		parent::__construct($manifest);
	}

	protected function getLocalizations(): array
	{
		return [
			'someKey' => ['someValue'],
			'anotherKey' => ['anotherValue']
		];
	}
};

beforeEach(function() {
	Monkey\setUp();
	setupMocks();

	// Setup Config mock.
	mock('alias:EightshiftBoilerplate\Config\Config')
		->shouldReceive([
			'getProjectName' => 'MyProject',
			'getProjectPath' => 'tests/data',
			'getProjectVersion' => '1.0',
		]);

	Functions\when('wp_register_style')->alias(function($args) {
		putenv("REGISTER_STYLE={$args}");
	});

	Functions\when('wp_enqueue_style')->alias(function($args) {
		putenv("ENQUEUE_STYLE={$args}");
	});

	Functions\when('wp_register_script')->alias(function($args) {
		putenv("REGISTER_SCRIPT={$args}");
	});

	Functions\when('wp_enqueue_script')->alias(function($args) {
		putenv("ENQUEUE_SCRIPT={$args}");
	});

	$localize = 'localize';
	Functions\when('wp_localize_script')->justReturn(putenv("SIDEAFFECT={$localize}"));

	$manifest = new ManifestExample();
	// We need to 'kickstart' the manifest registration manually during tests.
	$manifest->setAssetsManifestRaw();

	$this->adminEnqueue = new EnqueueAdminExampleTest($manifest);

	$this->hookSuffix = 'test';
});

afterEach(function() {
	Monkey\tearDown();

	putenv('REGISTER_STYLE');
	putenv('ENQUEUE_STYLE');
	putenv('REGISTER_SCRIPT');
	putenv('ENQUEUE_SCRIPT');
	putenv('SIDEAFFECT');
});


test('Register method will call login_enqueue_scripts and admin_enqueue_scripts hook', function () {
	$this->adminEnqueue->register();

	$this->assertSame(10, has_action('login_enqueue_scripts', 'Tests\Unit\Enqueue\Admin\EnqueueAdminExampleTest->enqueueStyles()'));
	$this->assertSame(50, has_action('admin_enqueue_scripts', 'Tests\Unit\Enqueue\Admin\EnqueueAdminExampleTest->enqueueStyles()'));
	$this->assertSame(10, has_action('admin_enqueue_scripts', 'Tests\Unit\Enqueue\Admin\EnqueueAdminExampleTest->enqueueScripts()'));
	$this->assertNotSame(10, has_action('wp_enqueue_scripts', 'Tests\Unit\Enqueue\Admin\EnqueueAdminExampleTest->enqueueStyles()'));
	$this->assertNotSame(10, has_action('wp_enqueue_scripts', 'Tests\Unit\Enqueue\Admin\EnqueueAdminExampleTest->enqueueScripts()'));
});

test('getAssetsPrefix method will return string', function () {
	$assetsPrefix = $this->adminEnqueue->getAssetsPrefix();

	$this->assertIsString($assetsPrefix, 'getAssetsPrefix method must return a string');
});

test('getAssetsVersion method will return string', function () {
	$assetsVersion = $this->adminEnqueue->getAssetsVersion();

	$this->assertIsString($assetsVersion, 'getAssetsVersion method must return a string');
});

test('enqueueStyles method enqueue styles in WP Admin', function () {
	$this->adminEnqueue->enqueueStyles($this->hookSuffix);

	$this->assertSame(getenv('REGISTER_STYLE'), 'MyProject-styles', 'Method enqueueStyles() failed to register style');
	$this->assertSame(getenv('ENQUEUE_STYLE'), 'MyProject-styles', 'Method enqueueStyles() failed to enqueue style');
});

test('enqueueScripts method enqueue scripts in WP Admin', function () {
	$this->adminEnqueue->enqueueScripts($this->hookSuffix);

	$this->assertSame(getenv('REGISTER_SCRIPT'), 'MyProject-scripts', 'Method enqueueStyles() failed to register style');
	$this->assertSame(getenv('ENQUEUE_SCRIPT'), 'MyProject-scripts', 'Method enqueueScripts() failed to enqueue style');
	$this->assertSame(getenv('SIDEAFFECT'), 'localize', 'Method wp_localize_script() failed');
});

test('Localization will return empty array if not initialized', function() {
	class ExampleLocalization extends AbstractAssets {

		public function getAssetsPrefix(): string
		{
			return 'prefix';
		}

		public function getAssetsVersion(): string
		{
			return '1.0.0';
		}

		public function register(): void
		{
		}

		public function getLocalizations(): array
		{
			return parent::getLocalizations();
		}
	}

	$localizationExample = new ExampleLocalization();

	$this->assertIsArray($localizationExample->getLocalizations());
	$this->assertEmpty($localizationExample->getLocalizations());
});
