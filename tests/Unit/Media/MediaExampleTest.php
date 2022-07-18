<?php

namespace Tests\Unit\Media;

use Brain\Monkey\Functions;
use EightshiftBoilerplate\Media\MediaExample;

beforeEach(function() {
	$this->media = new MediaExample();
});

test('Register method will call init hook', function () {
	$this->media->register();

	$this->assertSame(20, has_action('after_setup_theme', 'EightshiftBoilerplate\Media\MediaExample->addThemeSupport()'));
	$this->assertSame(10, has_filter('wp_generate_attachment_metadata', 'EightshiftBoilerplate\Media\MediaExample->generateWebPMedia()'), 2);
	$this->assertSame(10, has_filter('wp_update_attachment_metadata', 'EightshiftBoilerplate\Media\MediaExample->generateWebPMedia()'), 2);
	$this->assertSame(10, has_action('delete_attachment', 'EightshiftBoilerplate\Media\MediaExample->deleteWebPMedia()'));
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
