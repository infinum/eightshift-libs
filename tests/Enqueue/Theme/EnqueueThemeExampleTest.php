<?php

namespace Tests\Unit\Enqueue\Theme;

use Brain\Monkey;
use Brain\Monkey\Functions;
use EightshiftBoilerplate\Enqueue\Theme\EnqueueThemeExample;
use EightshiftBoilerplate\Manifest\ManifestExample;

use function Tests\setupMocks;
use function Tests\mock;

beforeEach(function() {
	Monkey\setUp();
	setupMocks();

	mock('alias:EightshiftBoilerplate\Config\Config')
	->shouldReceive('getProjectName', 'getProjectVersion')
	->andReturn('tests/data', '1.0');
	
	// Functions\when('wp_register_style')->justReturn();
	// Functions\when('wp_enqueue_style')->justReturn();
	
	// Functions\when('wp_register_script')->justReturn();
	// Functions\when('wp_enqueue_script')->justReturn();
	// Functions\when('wp_localize_script')->justReturn(true);

	$manifest = new ManifestExample();
	$this->example = new EnqueueThemeExample($manifest);
});

afterEach(function() {
	Monkey\tearDown();
});


test('Register method will call wp_enqueue_scripts hook', function () {
	$this->example->register();

	$this->assertSame(10, has_action('wp_enqueue_scripts', 'EightshiftBoilerplate\Enqueue\Theme\EnqueueThemeExample->enqueueStyles()'));
	$this->assertSame(10, has_action('wp_enqueue_scripts', 'EightshiftBoilerplate\Enqueue\Theme\EnqueueThemeExample->enqueueScripts()'));
	$this->assertNotSame(10, has_action('admin_enqueue_scripts', 'EightshiftBoilerplate\Enqueue\Theme\EnqueueThemeExample->enqueueStyles()'));
	$this->assertNotSame(10, has_action('admin_enqueue_scripts', 'EightshiftBoilerplate\Enqueue\Theme\EnqueueThemeExample->enqueueScripts()'));
});

test('getAssetsPrefix method will return string', function () {
	$assetsPrefix = $this->example->getAssetsPrefix();

	$this->assertIsString($assetsPrefix, 'getAssetsPrefix method must return a string');
});

test('getAssetsVersion method will return string', function () {
	$assetsVersion = $this->example->getAssetsVersion();

	$this->assertIsString($assetsVersion, 'getAssetsVersion method must return a string');
});

test('enqueueStyles method will enqueue styles in a theme', function () {
	Functions\when('wp_register_style')->alias(function($args) {
		putenv("REGISTER_STYLE={$args}");
	});

	Functions\when('wp_enqueue_style')->alias(function($args) {
		putenv("ENQUEUE_STYLE={$args}");
	});
	
	$this->example->enqueueStyles();
	$this->assertSame(getenv('REGISTER_STYLE'), 'tests/data-theme-styles', "Method enqueueStyles() failed to register style");
	$this->assertSame(getenv('ENQUEUE_STYLE'), 'tests/data-theme-styles', "Method enqueueStyles() failed to enqueue style");
});

test('enqueueScripts method will will enqueue scripts in a theme', function () {
	Functions\when('wp_register_script')->alias(function($args) {
		putenv("REGISTER_SCRIPT={$args}");
	});

	Functions\when('wp_enqueue_script')->alias(function($args) {
		putenv("ENQUEUE_SCRIPT={$args}");
	});
	
	$localize = 'localize';
	Functions\when('wp_localize_script')->justReturn(putenv("SIDEAFFECT={$localize}"));

	$this->example->enqueueScripts();

	$this->assertSame(getenv('REGISTER_SCRIPT'), 'tests/data-scripts', "Method enqueueStyles() failed to register style");
	$this->assertSame(getenv('ENQUEUE_SCRIPT'), 'tests/data-scripts', "Method enqueueScripts() failed to enqueue style");
	$this->assertEquals(getenv('SIDEAFFECT'), $localize);

	// Cleanup.
	putenv('SIDEAFFECT=');
});
