<?php

namespace Tests\Unit\Login;

use EightshiftBoilerplate\Main\MainExample;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function() {
	setBeforeEach();

	$this->main = new MainExample([], '');
});

afterEach(function() {
	setAfterEach();
});

test('Register method will call init hook', function () {
	$this->main->register();

	$this->assertSame(10, has_action('after_setup_theme', 'EightshiftBoilerplate\Main\MainExample->registerServices()'));
});
