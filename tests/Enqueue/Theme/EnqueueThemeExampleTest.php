<?php

namespace Tests\Unit\Enqueue\Theme;

use Brain\Monkey;
use EightshiftBoilerplate\Enqueue\Theme\EnqueueThemeExample;
use EightshiftBoilerplate\Manifest\ManifestExample;

use function Tests\setupMocks;

beforeEach(function() {
	Monkey\setUp();
	setupMocks();

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
	$this->assertNotSame(10, has_action('admin_enqueue_scripts', 'EightshiftBoilerplate\Enqueue\Theme\EnqueueThemeExample->enqueueScripts()'));
	$this->assertNotSame(10, has_action('admin_enqueue_scripts', 'EightshiftBoilerplate\Enqueue\Theme\EnqueueThemeExample->enqueueScripts()'));
});
