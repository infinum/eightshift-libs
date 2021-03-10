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

test('if theme options actions are registered', function () {
	$this->example->register();

	$this->assertTrue(\method_exists($this->example, 'register'));
	$this->assertEquals(10, has_action('acf/init', [$this->example, 'createThemeOptionsPage']));
	$this->assertEquals(10, has_action('acf/init', [$this->example, 'registerThemeOptions']));
});
