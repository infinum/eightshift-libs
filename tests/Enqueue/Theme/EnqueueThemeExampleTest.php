<?php

namespace Tests\Unit\Enqueue\Theme;

use Brain\Monkey;
use Brain\Monkey\Functions;
use EightshiftBoilerplate\Enqueue\Theme\EnqueueThemeExample;
use EightshiftLibs\Manifest\ManifestInterface;
use EightshiftBoilerplate\Manifest\ManifestExample;

use function Tests\setupMocks;
use function Tests\mock;

class EnqueueThemeTest extends EnqueueThemeExample {

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

	$this->themeEnqueue = new EnqueueThemeTest($manifest);
});

afterEach(function() {
	Monkey\tearDown();

	putenv('REGISTER_STYLE');
	putenv('ENQUEUE_STYLE');
	putenv('REGISTER_SCRIPT');
	putenv('ENQUEUE_SCRIPT');
	putenv('SIDEAFFECT');
});


test('Register method will call wp_enqueue_scripts hook', function () {
	$this->themeEnqueue->register();

	$this->assertSame(10, has_action('wp_enqueue_scripts', 'Tests\Unit\Enqueue\Theme\EnqueueThemeTest->enqueueStyles()'));
	$this->assertSame(10, has_action('wp_enqueue_scripts', 'Tests\Unit\Enqueue\Theme\EnqueueThemeTest->enqueueScripts()'));
	$this->assertNotSame(10, has_action('admin_enqueue_scripts', 'Tests\Unit\Enqueue\Theme\EnqueueThemeTest->enqueueStyles()'));
	$this->assertNotSame(10, has_action('admin_enqueue_scripts', 'Tests\Unit\Enqueue\Theme\EnqueueThemeTest->enqueueScripts()'));
});

test('getAssetsPrefix method will return string', function () {
	$assetsPrefix = $this->themeEnqueue->getAssetsPrefix();

	$this->assertIsString($assetsPrefix, 'getAssetsPrefix method must return a string');
});

test('getAssetsVersion method will return string', function () {
	$assetsVersion = $this->themeEnqueue->getAssetsVersion();

	$this->assertIsString($assetsVersion, 'getAssetsVersion method must return a string');
});

test('enqueueStyles method will enqueue styles in a theme', function () {
	$this->themeEnqueue->enqueueStyles();
	$this->assertSame(getenv('REGISTER_STYLE'), 'MyProject-theme-styles', 'Method enqueueStyles() failed to register style');
	$this->assertSame(getenv('ENQUEUE_STYLE'), 'MyProject-theme-styles', 'Method enqueueStyles() failed to enqueue style');
});

test('enqueueScripts method will enqueue scripts in a theme', function () {

	$this->themeEnqueue->enqueueScripts();
	$this->assertSame(getenv('REGISTER_SCRIPT'), 'MyProject-scripts', 'Method enqueueStyles() failed to register style');
	$this->assertSame(getenv('ENQUEUE_SCRIPT'), 'MyProject-scripts', 'Method enqueueScripts() failed to enqueue style');
	$this->assertSame(getenv('SIDEAFFECT'), 'localize', 'Method wp_localize_script() failed');
});
