<?php

namespace Tests\Unit\ThemeOptions;

use Brain\Monkey;
use EightshiftBoilerplate\ThemeOptions\ThemeOptionsExample;

use function Tests\setupMocks;

beforeEach(function() {
	Monkey\setUp();
	setupMocks();

	$this->example = new ThemeOptionsExample();
});

afterEach(function() {
	Monkey\tearDown();
});

test('Register method will bail out if ACF is not registered/activated', function () {
	$this->assertNull($this->example->register());
});

test('if theme options actions are registered', function () {
	$this->example->register();

	$this->assertTrue(\method_exists($this->example, 'register'));
	$this->assertSame(10, has_action('acf/init', [$this->example, 'createThemeOptionsPage']));
	$this->assertSame(10, has_action('acf/init', [$this->example, 'registerThemeOptions']));
});

test('Method for adding ACF Theme Options page exists', function () {
	$this->example->createThemeOptionsPage();

	$this->assertTrue(\method_exists($this->example, 'createThemeOptionsPage'));
	$this->assertTrue(\function_exists('acf_add_options_page'));
});

test('Method for adding ACF fields to Theme Options page exists', function () {
	$this->example->registerThemeOptions();

	$this->assertTrue(\method_exists($this->example, 'registerThemeOptions'));
	$this->assertTrue(\function_exists('acf_add_local_field_group'));
});
