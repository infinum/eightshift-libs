<?php

namespace Tests\Unit\Login;

use Brain\Monkey;
use Brain\Monkey\Functions;
use EightshiftBoilerplate\Main\MainExample;

use function Tests\setupMocks;

beforeEach(function() {
	Monkey\setUp();
	setupMocks();

	$this->main = new MainExample([], '');
});

afterEach(function() {
	Monkey\tearDown();
});

test('Register method will call init hook', function () {
	$this->main->register();

	$this->assertSame(10, has_action('after_setup_theme', 'EightshiftBoilerplate\Main\MainExample->registerServices()'));
});
