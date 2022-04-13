<?php

namespace Tests\Unit\Login;

use Brain\Monkey;
use EightshiftBoilerplate\Main\MainExample;

use function Tests\setupUnitTestMocks;

beforeEach(function() {
	Monkey\setUp();
	setupUnitTestMocks();

	$this->main = new MainExample([], '');
});

afterEach(function() {
	Monkey\tearDown();
});

test('Register method will call init hook', function () {
	$this->main->register();

	$this->assertSame(10, has_action('after_setup_theme', 'EightshiftBoilerplate\Main\MainExample->registerServices()'));
});
