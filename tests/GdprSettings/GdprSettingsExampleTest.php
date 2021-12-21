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

test('GDPR Settings actions are registered', function () {
	$this->example->register();

	$this->assertTrue(\method_exists($this->example, 'register'));
	$this->assertSame(20, has_action('acf/init', [$this->example, 'createGdprSettingsPage']));
	$this->assertSame(20, has_action('acf/init', [$this->example, 'registerGdprSettings']));
});
