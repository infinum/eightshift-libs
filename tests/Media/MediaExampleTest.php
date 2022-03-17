<?php

namespace Tests\Unit\Media;

use Brain\Monkey;
use Brain\Monkey\Functions;
use EightshiftBoilerplate\Media\MediaExample;

use function Tests\setupMocks;

beforeEach(function() {
	Monkey\setUp();
	setupMocks();

	$this->media = new MediaExample();
});

afterEach(function() {
	Monkey\tearDown();
});

test('Register method will call init hook', function () {
	$this->media->register();

	$this->assertSame(20, has_action('after_setup_theme', 'EightshiftBoilerplate\Media\MediaExample->addThemeSupport()'));
});

test('addThemeSupport method will call add_theme_support() function with different arguments', function () {
	Functions\when('add_theme_support')->alias(function($arg) {
		$envName = strtoupper($arg);
		$envName = \str_replace('-', '_', $envName);
		putenv("{$envName}=true");
	});

	$this->media->addThemeSupport();

	$this->assertSame(\getenv('TITLE_TAG'), 'true', "Method addThemeSupport() didn't add theme support for title-tag");
	$this->assertSame(\getenv('HTML5'), 'true', "Method addThemeSupport() didn't add theme support for html5");
	$this->assertSame(\getenv('POST_THUMBNAILS'), 'true', "Method addThemeSupport() didn't add theme support for post-thumbnails");

});
