<?php

namespace Tests\Unit\Helpers;

use EightshiftLibs\Helpers\Components;

use Brain\Monkey\Functions;

use function Tests\buildTestBlocks;

beforeEach(function () {
	buildTestBlocks();
});

// ------------------------------------------
// outputCssVariablesGlobal
// ------------------------------------------

test('Asserts that outputCssVariablesGlobal returns the correct CSS variables from global manifest', function () {
	$output = Components::outputCssVariablesGlobal();

	expect($output)
		->toBeString()
		->toContain('</style>')
		->toContain(':root {')
		->toContain('--global-colors-primary: #C3151B;')
		->toContain('--global-colors-primary-values: 195 21 27;')
		->not->toContain('--button-content:');
});

test('Asserts that outputCssVariablesGlobal returns empty string if global manifest data is not provided', function () {
	$output = Components::outputCssVariablesGlobal();

	expect($output)
		->toBeString()
		->not->toContain('<style>');
});

// ------------------------------------------
// outputCssVariables
// ------------------------------------------

test('outputCssVariables returns empty string if global breakpoints are missing', function () {
	$output = Components::outputCssVariables(
		[],
		[],
		'uniqueString',
		['namespace' => 'eightshift']
	);

	expect($output)
		->toBeString()
		->toBeEmpty();
});

test('outputCssVariables returns empty string if global attributes or manifest are missing', function () {
	$output = Components::outputCssVariables(
		['attribute'],
		[],
		'uniqueString'
	);

	expect($output)
		->toBeString()
		->toBeEmpty();

	$outputSecond = Components::outputCssVariables(
		[],
		['manifest'],
		'uniqueString'
	);

	expect($outputSecond)
		->toBeString()
		->toBeEmpty();
});

test('outputCssVariables returns empty string if variable keys are missing in manifest', function () {
	$output = Components::outputCssVariables(
		['attribute'],
		['manifest'],
		'uniqueString',
	);

	expect($output)
		->toBeString()
		->toBeEmpty();
});

test('outputCssVariables works when correct attributes are passed to it', function () {
	$manifest = Components::getManifest(Components::getProjectPaths('blocksDestinationComponents', 'variables'));

	Components::setConfigOutputCssGlobally(false);
	Components::setConfigOutputCssOptimize(false);

	$attributes = [
		'variableValue' => 'value3',
	];

	$output = Components::outputCssVariables(
		$attributes,
		$manifest,
		'uniqueString'
	);

	expect($output)
		->toBeString()
		->not->toBeEmpty();
});

test('outputCssVariables works when correct attributes are passed to it and has a unique name', function () {
	$manifest = Components::getManifest(Components::getProjectPaths('blocksDestinationComponents', 'variables'));

	Components::setConfigOutputCssGlobally(false);
	Components::setConfigOutputCssOptimize(false);

	$attributes = [
		'variableValue' => 'value3',
	];

	$output = Components::outputCssVariables(
		$attributes,
		$manifest,
		'uniqueString',
		[],
		'customNameAttribute'
	);

	expect($output)
		->toBeString()
		->not->toBeEmpty();
});

test('outputCssVariables outputs the style tag in default way if the outputCssGlobally is set to false', function () {
	Components::setConfigOutputCssGlobally(false);
	Components::setConfigOutputCssOptimize(false);

	$manifest = Components::getManifest(Components::getProjectPaths('blocksDestinationComponents', 'variables'));

	$attributes = [
		'variableValue' => 'value3',
	];

	$output = Components::outputCssVariables(
		$attributes,
		$manifest,
		'uniqueString',
		[],
		'customNameAttribute'
	);

	expect($output)
		->toBeString()
		->not->toBeEmpty();
});

test('outputCssVariables outputs the style tag in inline way if the outputCssGlobally is set to true', function () {
	$manifest = Components::getManifest(Components::getProjectPaths('blocksDestinationComponents', 'variables'));

	$attributes = [
		'variableValue' => 'value3',
	];

	$output = Components::outputCssVariables(
		$attributes,
		$manifest,
		'uniqueString',
		[],
		'customNameAttribute'
	);

	$styles = Components::getStyles();

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
	Components::setConfigOutputCssGlobally(false);
	Components::setConfigOutputCssOptimize(false);

	$result = Components::outputCssVariablesInline();

	expect($result)->toBeString()->toEqual('');
});

test('Asserts that "outputCssVariablesInline" will return empty style but output aditional styles.', function () {
	Components::setStyles([]);

	$result = Components::outputCssVariablesInline();

	expect($result)->toBeString()->toEqual("<style id='esCssVariablesTest'> :root {--es-loader-opacity: 1;}</style>");
});

test('Asserts that "outputCssVariablesInline" will return empty style tag if styles are empty.', function () {
	Components::setConfigOutputCssGloballyAdditionalStyles([]);
	Components::setStyles([]);

	$result = Components::outputCssVariablesInline();

	expect($result)->toBeString()->toEqual("<style id='esCssVariablesTest'> </style>");
});

test('Asserts that "outputCssVariablesInline" will return style tag with the correct styles.', function () {
	Components::setConfigOutputCssOptimize(false);

	;
	$manifest = Components::getManifest(Components::getProjectPaths('blocksDestinationComponents', 'variables'));
	$attributes = [
		'variableValue' => 'value3',
	];

	Components::outputCssVariables($attributes, $manifest, 'unique');

	$result = Components::outputCssVariablesInline();

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
	$converted = Components::hexToRgb($input);

	$this->assertIsString($converted);
	$this->assertSame($converted, $output);
})->with('hexToRgbValid');

test('Check that hexToRgb returns the fallback for an invalid hex code', function ($input, $output) {
	$converted = Components::hexToRgb($input);

	$this->assertIsString($converted);
	$this->assertSame($converted, $output);
})->with('hexToRgbInvalid');


// ------------------------------------------
// getUnique
// ------------------------------------------

test('Asserts that getUnique function will return some random string', function () {

	Functions\when('wp_rand')->justReturn(mt_rand());
	$unique = Components::getUnique();

	expect($unique)
		->toBeString();
});
