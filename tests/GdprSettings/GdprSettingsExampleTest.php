<?php

namespace Tests\Unit\GdprSettings;

use Brain\Monkey;
use EightshiftBoilerplate\GdprSettings\GdprSettingsExample;

use function Tests\setupMocks;

beforeEach(function() {
	Monkey\setUp();
	setupMocks();

	$this->example = new GdprSettingsExample();
});

afterEach(function() {
	Monkey\tearDown();
});

test('Register method will bail out if ACF is not registered/activated', function () {
	$this->assertNull($this->example->register());
});

test('GDPR Settings actions are registered', function () {
	$this->example->register();

	$this->assertTrue(\method_exists($this->example, 'register'));
	$this->assertSame(20, has_action('acf/init', [$this->example, 'createGdprSettingsPage']));
	$this->assertSame(20, has_action('acf/init', [$this->example, 'registerGdprSettings']));
});

test('Method for adding GDPR Settings as ACF Options page exists', function () {
	$this->example->createGdprSettingsPage();

	$this->assertTrue(\method_exists($this->example, 'createGdprSettingsPage'));
	$this->assertTrue(\function_exists('acf_add_options_page'));
});

test('Method for adding ACF fields to GDPR Settings page exists', function () {
	$this->example->registerGdprSettings();

	$this->assertTrue(\method_exists($this->example, 'registerGdprSettings'));
	$this->assertTrue(\function_exists('acf_add_local_field_group'));
});
