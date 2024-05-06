<?php

namespace Tests\Unit\ThemeOptions;

use EightshiftLibs\ThemeOptions\ThemeOptionsCli;
use Infinum\ThemeOptions\ThemeOptions;

use function Tests\getMockArgs;
use function Tests\reqOutputFiles;

beforeEach(function () {
	$themeOptionsCliMock = new ThemeOptionsCli('boilerplate');
	$themeOptionsCliMock([], getMockArgs($themeOptionsCliMock->getDefaultArgs()));

	reqOutputFiles(
		'ThemeOptions/ThemeOptions.php',
	);
});

test('Register method will bail out if ACF is not registered/activated', function () {
	expect((new ThemeOptions())->register())->toBeNull();
});

test('if theme options actions are registered', function () {
	mock('alias:ACF');

	(new ThemeOptions())->register();

	expect(has_action('acf/init', 'Infinum\ThemeOptions\ThemeOptions->createThemeOptionsPage()'))->toBe(10);
	expect(has_action('acf/init', 'Infinum\ThemeOptions\ThemeOptions->registerThemeOptions()'))->toBe(10);
});

test('Method for adding ACF Theme Options page exists', function () {
	expect(\method_exists(new ThemeOptions(), 'createThemeOptionsPage'))->toBeTrue();
});

test('Method for adding ACF fields to Theme Options page exists', function () {
	expect(\method_exists(new ThemeOptions(), 'registerThemeOptions'))->toBeTrue();
});
