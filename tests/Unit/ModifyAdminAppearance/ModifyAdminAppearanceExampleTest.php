<?php

namespace Tests\Unit\Login;

use Brain\Monkey\Functions;
use EightshiftLibs\ModifyAdminAppearance\ModifyAdminAppearanceCli;
use Infinum\ModifyAdminAppearance\ModifyAdminAppearance;

use function Tests\getMockArgs;
use function Tests\reqOutputFiles;

beforeEach(function() {
	$modifyAdminAppearanceCliMock = new ModifyAdminAppearanceCli('boilerplate');
	$modifyAdminAppearanceCliMock([], getMockArgs($modifyAdminAppearanceCliMock->getDefaultArgs()));

	reqOutputFiles(
		'ModifyAdminAppearance/ModifyAdminAppearance.php',
	);
});

test('Register method will call init hook', function () {
	(new ModifyAdminAppearance())->register();

	$this->assertSame(10, has_filter('get_user_option_admin_color', 'Infinum\ModifyAdminAppearance\ModifyAdminAppearance->adminColor()'));
});

test('Asserts if adminColor returns string', function () {

	Functions\when('wp_get_environment_type')->justReturn('staging');

	$output = (new ModifyAdminAppearance())->adminColor();

	$this->assertStringContainsString('blue', $output);
});

test('Asserts if wp_get_environment_type is empty', function () {

	Functions\when('wp_get_environment_type')->justReturn(null);

	$output = (new ModifyAdminAppearance())->adminColor();

	$this->assertStringContainsString('fresh', $output);
	$this->assertStringNotContainsString('sunrise', $output);
});
