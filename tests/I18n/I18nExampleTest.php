<?php

namespace Tests\Unit\I18n;

use Brain\Monkey;
use EightshiftBoilerplate\I18n\I18nExample;

use function Tests\setupMocks;

beforeEach(function() {
	Monkey\setUp();
	setupMocks();

	$this->example = new I18nExample();
});

afterEach(function() {
	Monkey\tearDown();
});


test('Register method will call init hook', function () {
	$this->example->register();

	$this->assertSame(20, has_action('after_setup_theme', 'EightshiftBoilerplate\I18n\I18nExample->loadThemeTextdomain()'));
});
