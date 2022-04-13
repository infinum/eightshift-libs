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
	$generatedMeta = \file_get_contents(\dirname(__FILE__, 4) . '/cliOutput/src/ThemeOptions/ThemeOptions.php');

	$this->assertStringContainsString('class ThemeOptions implements ServiceInterface', $generatedMeta);
	$this->assertStringContainsString('acf_add_options_page', $generatedMeta);
	$this->assertStringContainsString('acf_add_local_field_group', $generatedMeta);
	$this->assertStringContainsString('createThemeOptionsPage', $generatedMeta);
	$this->assertStringContainsString('registerThemeOptions', $generatedMeta);
	$this->assertStringNotContainsString('someRandomMethod', $generatedMeta);
});

test('Custom theme options CLI documentation is correct', function () {
	$themeOptions = $this->themeOptions;

	$documentation = $themeOptions->getDoc();

	$descKey = 'shortdesc';

	$this->assertIsArray($documentation);
	$this->assertArrayHasKey($descKey, $documentation);
	$this->assertSame('Generates project Theme Options class using ACF.', $documentation[$descKey]);
});
