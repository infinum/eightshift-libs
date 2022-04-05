<?php

namespace Tests\Unit\Helpers;

use EightshiftLibs\Helpers\Components;

use Brain\Monkey;
use Brain\Monkey\Functions;
use EightshiftBoilerplate\Blocks\BlocksExample;

use function Tests\setupMocks;

beforeAll(function () {
	Monkey\setUp();
	setupMocks();
});

afterAll(function() {
	Monkey\tearDown();
});

beforeEach(function() {
	(new BlocksExample())->getBlocksDataFullRaw();
});

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
	$output = Components::outputCssVariablesGlobal([]);

	expect($output)
		->toBeString()
		->not->toContain('<style>');
});

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
	$globalManifest = Components::getManifestDirect(dirname(__FILE__, 2) . '/data/src/Blocks');

	$output = Components::outputCssVariables(
		['attribute'],
		[],
		'uniqueString',
		$globalManifest
	);

	expect($output)
		->toBeString()
		->toBeEmpty();

	$outputSecond = Components::outputCssVariables(
		[],
		['manifest'],
		'uniqueString',
		$globalManifest
	);

	expect($outputSecond)
		->toBeString()
		->toBeEmpty();
});

test('outputCssVariables returns empty string if variable keys are missing in manifest', function () {
	$globalManifest = Components::getManifestDirect(dirname(__FILE__, 2) . '/data/src/Blocks');

	$output = Components::outputCssVariables(
		['attribute'],
		['manifest'],
		'uniqueString',
		$globalManifest
	);

	expect($output)
		->toBeString()
		->toBeEmpty();
});

test('outputCssVariables works when correct attributes are passed to it', function () {
	$manifest = Components::getManifestDirect(dirname(__FILE__, 2) . '/data/src/Blocks/components/variables');
	$globalManifest = Components::getManifestDirect(dirname(__FILE__, 2) . '/data/src/Blocks');

	$attributes = [
		'variableValue' => 'value3',
	];

	$output = Components::outputCssVariables(
		$attributes,
		$manifest,
		'uniqueString',
		$globalManifest
	);

	expect($output)
		->toBeString()
		->not->toBeEmpty();
});

test('outputCssVariables works when correct attributes are passed to it and has a unique name', function () {
	$manifest = Components::getManifestDirect(dirname(__FILE__, 2) . '/data/src/Blocks/components/variables');
	$globalManifest = Components::getManifestDirect(dirname(__FILE__, 2) . '/data/src/Blocks');

	$attributes = [
		'variableValue' => 'value3',
	];

	$output = Components::outputCssVariables(
		$attributes,
		$manifest,
		'uniqueString',
		$globalManifest,
		'customNameAttribute'
	);

	expect($output)
		->toBeString()
		->not->toBeEmpty();
});

test('outputCssVariables outputs the style tag if the outputCssVariablesGlobally is set to false', function () {
	$globalManifest = Components::getManifestDirect(dirname(__FILE__, 2) . '/data/src/Blocks');
	$esBlocks[$globalManifest['namespace']]['config']['outputCssVariablesGlobally'] = false;

	$manifest = Components::getManifestDirect(dirname(__FILE__, 2) . '/data/src/Blocks/components/variables');

	$attributes = [
		'variableValue' => 'value3',
	];

	$output = Components::outputCssVariables(
		$attributes,
		$manifest,
		'uniqueString',
		$globalManifest,
		'customNameAttribute'
	);

	expect($output)
		->toBeString()
		->not->toBeEmpty();

});

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

test('Asserts that getUnique function will return some random string', function () {

	Functions\when('wp_rand')->justReturn(mt_rand());
	$unique = Components::getUnique();

	expect($unique)
		->toBeString();
});
