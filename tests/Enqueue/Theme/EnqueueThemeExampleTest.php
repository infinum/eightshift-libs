<?php

namespace Tests\Unit\Enqueue\Theme;

use Brain\Monkey;
use EightshiftBoilerplate\Enqueue\Theme\EnqueueThemeExample;
use EightshiftBoilerplate\Manifest\ManifestExample;

use function Tests\setupMocks;
use function Tests\mock;

beforeEach(function() {
	Monkey\setUp();
	setupMocks();

	mock('alias:EightshiftBoilerplate\Config\Config')
	->shouldReceive('getProjectName', 'getProjectVersion')
	->andReturn('tests/data');

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
