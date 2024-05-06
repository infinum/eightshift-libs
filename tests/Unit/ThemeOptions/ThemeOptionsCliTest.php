<?php

namespace Tests\Unit\ThemeOptions;

use EightshiftLibs\Helpers\Helpers;
use EightshiftLibs\ThemeOptions\ThemeOptionsCli;

use function Tests\getMockArgs;

beforeEach(function () {
	$this->mock = new ThemeOptionsCli('boilerplate');
});

afterEach(function () {
	unset($this->mock);
});

test('Custom theme options CLI command will correctly copy the theme options class with defaults', function () {
	$mock = $this->mock;
	$mock([], getMockArgs());

	$sep = \DIRECTORY_SEPARATOR;
	$output = \file_get_contents(Helpers::getProjectPaths('srcDestination', "ThemeOptions{$sep}ThemeOptions.php"));

		expect($output)
			->toBeString()
			->toContain(
				'class ThemeOptions implements ServiceInterface',
				'acf_add_options_page',
				'acf_add_local_field_group',
				'createThemeOptionsPage',
				'registerThemeOptions'
			)
			->not->toContain('someRandomMethod');
});

test('Custom theme options CLI documentation is correct', function () {
	expect($this->mock->getDoc())->toBeArray();
	$mock = $this->mock;

	$mock = $mock->getDoc();

	$descKey = 'shortdesc';

		expect($mock)
			->toBeArray()
			->toHaveKey($descKey);

		expect($mock[$descKey])
			->toBeString()
			->toBe('Create project Theme Options service class using ACF plugin.');
});
