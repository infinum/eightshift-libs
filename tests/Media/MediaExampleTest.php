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

  $arg = '';

  Functions\when('add_theme_support')->alias(function($arg) {
		$envName = strtoupper($arg);
    putenv("{$envName}=true");
		var_dump($envName);
	});

  $this->media->addThemeSupport();

  $this->assertEquals(getenv('THEME_SUPPORT'), 'title-tag');
  $this->assertEquals(getenv('THEME_SUPPORT'), 'html5');
  $this->assertEquals(getenv('THEME_SUPPORT'), 'post-thumbnails');

});
