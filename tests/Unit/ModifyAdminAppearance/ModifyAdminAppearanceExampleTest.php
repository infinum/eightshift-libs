<?php

namespace Tests\Unit\Login;

use Brain\Monkey;
use Brain\Monkey\Functions;
use EightshiftBoilerplate\ModifyAdminAppearance\ModifyAdminAppearanceExample;

use function Tests\setupUnitTestMocks;

beforeEach(function() {
	Monkey\setUp();
	setupUnitTestMocks();

	$this->modifyAdminAppearance = new ModifyAdminAppearanceExample();
});

afterEach(function() {
	Monkey\tearDown();
});

test('Register method will call init hook', function () {
	$this->modifyAdminAppearance->register();

	$this->assertSame(10, has_filter('get_user_option_admin_color', 'EightshiftBoilerplate\ModifyAdminAppearance\ModifyAdminAppearanceExample->adminColor()'));
});

test('Asserts if adminColor returns string', function () {

	Functions\when('wp_get_environment_type')->justReturn('staging');

	$output = $this->modifyAdminAppearance->adminColor();

	$this->assertStringContainsString('blue', $output);
});

test('Asserts if wp_get_environment_type is empty', function () {

	Functions\when('wp_get_environment_type')->justReturn(null);

	$output = $this->modifyAdminAppearance->adminColor();

	$this->assertStringContainsString('fresh', $output);
	$this->assertStringNotContainsString('sunrise', $output);
});
