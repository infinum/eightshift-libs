<?php

namespace Tests\Unit\Helpers;

use Brain\Monkey\Functions;
use EightshiftLibs\Helpers\Helpers;

use function Tests\buildTestBlocks;

beforeEach(function () {
	buildTestBlocks();
});

// ------------------------------------------
// outputCssVariablesGlobal
// ------------------------------------------

test('Asserts that outputCssVariablesGlobal returns the correct CSS variables from global manifest', function () {
	$output = Helpers::outputCssVariablesGlobal();

	expect($output)
		->toBeString()
		->toContain('</style>')
		->toContain(':root {')
		->toContain('--global-colors-primary: #C3151B;')
		->toContain('--global-colors-primary-values: 195 21 27;')
		->not->toContain('--button-content:');
});

test('Asserts that outputCssVariablesGlobal returns empty string if global manifest data is not provided', function () {
	$output = Helpers::outputCssVariablesGlobal();

	expect($output)
		->toBeString()
		->not->toContain('<style>');
});

// ------------------------------------------
// outputCssVariables
// ------------------------------------------

test('outputCssVariables returns empty string if global breakpoints are missing', function () {
	$output = Helpers::outputCssVariables(
		[],
		[],
		'uniqueString'
	);

	expect($output)
		->toBeString()
		->toBeEmpty();
});

test('outputCssVariables returns empty string if global attributes or manifest are missing', function () {
	$output = Helpers::outputCssVariables(
		['attribute'],
		[],
		'uniqueString'
	);

	expect($output)
		->toBeString()
		->toBeEmpty();

	$outputSecond = Helpers::outputCssVariables(
		[],
		['manifest'],
		'uniqueString'
	);

	expect($outputSecond)
		->toBeString()
		->toBeEmpty();
});

test('outputCssVariables returns empty string if variable keys are missing in manifest', function () {
	$output = Helpers::outputCssVariables(
		['attribute'],
		['manifest'],
		'uniqueString',
	);

	expect($output)
		->toBeString()
		->toBeEmpty();
});

test('outputCssVariables works when correct attributes are passed to it', function () {
	$manifest = Helpers::getManifestByDir(Helpers::getProjectPaths('blocksDestinationComponents', 'variables'));

	Helpers::setConfigOutputCssGlobally(false);
	Helpers::setConfigOutputCssOptimize(false);

	$attributes = [
		'variableValue' => 'value3',
	];

	$output = Helpers::outputCssVariables(
		$attributes,
		$manifest,
		'uniqueString'
	);

	expect($output)
		->toBeString()
		->not->toBeEmpty();
});

test('outputCssVariables works when correct attributes are passed to it and has a unique name', function () {
	$manifest = Helpers::getManifestByDir(Helpers::getProjectPaths('blocksDestinationComponents', 'variables'));

	Helpers::setConfigOutputCssGlobally(false);
	Helpers::setConfigOutputCssOptimize(false);

	$attributes = [
		'variableValue' => 'value3',
	];

	$output = Helpers::outputCssVariables(
		$attributes,
		$manifest,
		'uniqueString',
		'customNameAttribute'
	);

	expect($output)
		->toBeString()
		->not->toBeEmpty();
});

test('outputCssVariables outputs the style tag in default way if the outputCssGlobally is set to false', function () {
	Helpers::setConfigOutputCssGlobally(false);
	Helpers::setConfigOutputCssOptimize(false);

	$manifest = Helpers::getManifestByDir(Helpers::getProjectPaths('blocksDestinationComponents', 'variables'));

	$attributes = [
		'variableValue' => 'value3',
	];

	$output = Helpers::outputCssVariables(
		$attributes,
		$manifest,
		'uniqueString',
		'customNameAttribute'
	);

	expect($output)
		->toBeString()
		->not->toBeEmpty();
});

test('outputCssVariables outputs the style tag in inline way if the outputCssGlobally is set to true', function () {
	$manifest = Helpers::getManifestByDir(Helpers::getProjectPaths('blocksDestinationComponents', 'variables'));

	$attributes = [
		'variableValue' => 'value3',
	];

	$output = Helpers::outputCssVariables(
		$attributes,
		$manifest,
		'uniqueString',
		'customNameAttribute'
	);

	$styles = Helpers::getStyles();

	expect($styles)
	->toBeArray()
	->toEqual([
		[
			'name' => 'customNameAttribute',
			'unique' => 'uniqueString',
			'variables' => [
				[
					'type' => 'min',
					'variable' => '--variable-value-default: default;',
					'value' => 0,
				],
				[
					'type' => 'max',
					'variable' => '--variable-value-tablet: tablet;',
					'value' => 991,
				]
			],
		],
	]);

	expect($output)
		->toBeString()
		->toBeEmpty();
});

// ------------------------------------------
// outputCssVariablesInline
// ------------------------------------------

test('Asserts that "outputCssVariablesInline" will return empty string if config flags are set to false.', function () {
	Helpers::setConfigOutputCssGlobally(false);
	Helpers::setConfigOutputCssOptimize(false);

	$result = Helpers::outputCssVariablesInline();

	expect($result)->toBeString()->toEqual('');
});

test('Asserts that "outputCssVariablesInline" will return empty style but output aditional styles.', function () {
	Helpers::setStyles([]);

	$result = Helpers::outputCssVariablesInline();

	expect($result)->toBeString()->toEqual("<style id='esCssVariablesTest'> :root {--es-loader-opacity: 1;}</style>");
});

test('Asserts that "outputCssVariablesInline" will return empty style tag if styles are empty.', function () {
	Helpers::setConfigOutputCssGloballyAdditionalStyles([]);
	Helpers::setStyles([]);

	$result = Helpers::outputCssVariablesInline();

	expect($result)->toBeString()->toEqual("<style id='esCssVariablesTest'> </style>");
});

test('Asserts that "outputCssVariablesInline" will return style tag with the correct styles.', function () {
	Helpers::setConfigOutputCssOptimize(false);

	;
	$manifest = Helpers::getManifestByDir(Helpers::getProjectPaths('blocksDestinationComponents', 'variables'));
	$attributes = [
		'variableValue' => 'value3',
	];

	Helpers::outputCssVariables($attributes, $manifest, 'unique');

	$result = Helpers::outputCssVariablesInline();

	expect($result)->toBeString()->toContain(
		"<style id='esCssVariablesTest'>",
		"--variable-value-default: default;",
		"@media (max-width:991px){"
	);
});

// ------------------------------------------
// hexToRgb
// ------------------------------------------

test('Check that hexToRgb returns the correct output for a valid hex code', function ($input, $output) {
	$converted = Helpers::hexToRgb($input);

	$this->assertIsString($converted);
	$this->assertSame($converted, $output);
})->with('hexToRgbValid');

test('Check that hexToRgb returns the fallback for an invalid hex code', function ($input, $output) {
	$converted = Helpers::hexToRgb($input);

	$this->assertIsString($converted);
	$this->assertSame($converted, $output);
})->with('hexToRgbInvalid');


// ------------------------------------------
// getUnique
// ------------------------------------------

test('Asserts that getUnique function will return some random string', function () {

	Functions\when('wp_rand')->justReturn(mt_rand());
	$unique = Helpers::getUnique();

	expect($unique)
		->toBeString();
});
