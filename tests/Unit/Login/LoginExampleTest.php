<?php

namespace Tests\Unit\Login;

use Brain\Monkey;
use Brain\Monkey\Functions;
use EightshiftBoilerplate\Login\LoginExample;

use function Tests\setupMocks;

beforeEach(function() {
	Monkey\setUp();
	setupMocks();

	$this->login = new LoginExample();
});

afterEach(function() {
	Monkey\tearDown();
});

test('Register method will call init hook', function () {
	$this->login->register();

	$this->assertSame(10, has_filter('login_headerurl', 'EightshiftBoilerplate\Login\LoginExample->customLoginUrl()'));
});

test('Asserts if customLoginUrl returns string', function () {

	Functions\when('home_url')->justReturn('custom/home/url');

	$output = $this->login->customLoginUrl();

	$this->assertStringContainsString('custom/home/url', $output);
});
