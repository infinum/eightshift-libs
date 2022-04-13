<?php

namespace Tests\Unit\ThemeOptions;

use Brain\Monkey;
use EightshiftBoilerplate\ThemeOptions\ThemeOptionsExample;

use function Tests\setupUnitTestMocks;

beforeEach(function() {
	Monkey\setUp();
	setupUnitTestMocks();

	$this->example = new ThemeOptionsExample();
});

afterEach(function() {
	Monkey\tearDown();
});

test('if theme options actions are registered', function () {
	$this->example->register();

	$this->assertTrue(\method_exists($this->example, 'register'));
	$this->assertSame(10, has_action('acf/init', [$this->example, 'createThemeOptionsPage']));
	$this->assertSame(10, has_action('acf/init', [$this->example, 'registerThemeOptions']));
});
