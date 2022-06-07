<?php

namespace Tests\Unit\ThemeOptions;

use EightshiftLibs\ThemeOptions\ThemeOptionsCli;

use function Tests\deleteCliOutput;
use function Tests\mock;

/**
 * Mock before tests.
 */
beforeEach(function () {
	$wpCliMock = mock('alias:WP_CLI');

	$wpCliMock
		->shouldReceive('success')
		->andReturnArg(0);

	$wpCliMock
		->shouldReceive('error')
		->andReturnArg(0);

	$this->themeOptions = new ThemeOptionsCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	deleteCliOutput();
});


test('Custom theme options CLI command will correctly copy the theme options class with defaults', function () {
	$themeOptions = $this->themeOptions;
	$themeOptions([], []);

	// Check the output dir if the generated method is correctly generated.
	$generatedMeta = \file_get_contents(\dirname(__FILE__, 3) . '/cliOutput/src/ThemeOptions/ThemeOptions.php');

		expect($generatedMeta)
			->toBeString()
			->toContain('class ThemeOptions implements ServiceInterface')
			->toContain('acf_add_options_page')
			->toContain('acf_add_local_field_group')
			->toContain('createThemeOptionsPage')
			->toContain('registerThemeOptions')
			->not->toContain('someRandomMethod');
});

test('Custom theme options CLI documentation is correct', function () {
	expect($this->themeOptions->getDoc())->toBeArray();
	$themeOptions = $this->themeOptions;

	$documentation = $themeOptions->getDoc();

	$descKey = 'shortdesc';

		expect($documentation)
			->toBeArray()
			->toHaveKey($descKey);

		expect($documentation[$descKey])
			->toBeString()
			->toBe('Create project Theme Options service class using ACF plugin.');
});
