<?php

namespace Tests\Unit\Login;

use EightshiftBoilerplate\Main\MainExample;

beforeEach(function() {
	$this->main = new MainExample([], '');
});

afterEach(function () {
	unset($this->main);
});

test('Register method will call init hook', function () {
	$this->main->register();

	$this->assertSame(10, has_action('after_setup_theme', 'EightshiftBoilerplate\Main\MainExample->registerServices()'));
});
