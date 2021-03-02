<?php

namespace Tests\Unit\ThemeOptions;

use EightshiftLibs\ThemeOptions\ThemeOptionsCli;

use function Tests\deleteCliOutput;

/**
 * Mock before tests.
 */
beforeEach(function () {
	$wpCliMock = \Mockery::mock('alias:WP_CLI');

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
	$output = dirname(__FILE__, 3) . '/cliOutput';

	deleteCliOutput($output);
});


test('Custom theme options CLI command will correctly copy the theme options class with defaults', function () {
	$themeOptions = $this->themeOptions;
	$themeOptions([], []);

	// Check the output dir if the generated method is correctly generated.
	$generatedMeta = file_get_contents(dirname(__FILE__, 3) . '/cliOutput/src/ThemeOptions/ThemeOptions.php');

	$this->assertStringContainsString('class ThemeOptions implements ServiceInterface', $generatedMeta);
	$this->assertStringContainsString('acf_add_options_page', $generatedMeta);
	$this->assertStringContainsString('acf_add_local_field_group', $generatedMeta);
	$this->assertStringContainsString('createThemeOptionsPage', $generatedMeta);
	$this->assertStringContainsString('registerThemeOptions', $generatedMeta);
});

test('Custom theme options CLI documentation is correct', function () {
	$themeOptions = $this->themeOptions;

	$documentation = $themeOptions->getDoc();

	$descKey = 'shortdesc';

	$this->assertIsArray($documentation);
	$this->assertArrayHasKey($descKey, $documentation);
	$this->assertEquals('Generates project Theme Options class using ACF.', $documentation[$descKey]);
});
