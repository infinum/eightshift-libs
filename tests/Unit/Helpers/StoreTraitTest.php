<?php

namespace Tests\Unit\Helpers;

use Brain\Monkey;
use EightshiftBoilerplate\Blocks\BlocksExample;
use EightshiftLibs\Helpers\Components;

use function Tests\setupUnitTestMocks;

beforeAll(function () {
	Monkey\setUp();
	setupUnitTestMocks();
});

afterAll(function() {
	Monkey\tearDown();
});

beforeEach(function () {
	(new BlocksExample())->getBlocksDataFullRaw();
});

afterEach(function () {
	global $esBlocks;
	$esBlocks = null;
});

// ------------------------------------------
// getBlocks
// ------------------------------------------

test('Asserts that "getBlocks" will return empty array if blocks are missing.', function () {
	global $esBlocks;
	$esBlocks = null;

	$result = Components::getBlocks();

	expect($result)
		->toBeArray()
		->toBe([]);
});

test('Asserts that "getBlocks" will return blocks list.', function () {
	$result = Components::getBlocks();

	expect($result)
		->toBeArray()
		->not->toBe([]);
});

// ------------------------------------------
// getBlock
// ------------------------------------------

test('Asserts that "getBlock" will return empty array if block is missing or wrong.', function () {
	$result = Components::getBlock('missing-block');

	expect($result)
		->toBeArray()
		->toBe([]);
});

test('Asserts that "getBlock" will return block manifest if name is correct.', function () {
	$result = Components::getBlock('button');

	expect($result)
		->toBeArray()
		->toHaveKey('blockName', 'button');
});

// ------------------------------------------
// getComponents
// ------------------------------------------

test('Asserts that "getComponents" will return empty array if components are missing.', function () {
	global $esBlocks;
	$esBlocks = null;

	$result = Components::getComponents();

	expect($result)
		->toBeArray()
		->toBe([]);
});

test('Asserts that "getComponents" will return components list.', function () {
	$result = Components::getComponents();

	expect($result)
		->toBeArray()
		->not->toBe([]);
});

// ------------------------------------------
// getComponent
// ------------------------------------------

test('Asserts that "getComponent" will return empty array if component is missing or wrong.', function () {
	$result = Components::getComponent('missing-component');

	expect($result)
		->toBeArray()
		->toBe([]);
});

test('Asserts that "getComponent" will return component manifest if name is correct.', function () {
	$result = Components::getComponent('button');

	expect($result)
		->toBeArray()
		->toHaveKey('componentName', 'button');
});

// ------------------------------------------
// getConfig
// ------------------------------------------

test('Asserts that "getConfig" will return config array.', function () {
	$result = Components::getConfig();

	expect($result)
		->toBeArray()
		->toHaveKeys(
			[
				'outputCssGlobally',
				'outputCssOptimize',
				'outputCssSelectorName',
			]
		);
});

// ------------------------------------------
// setConfigFlags
// ------------------------------------------

test('Asserts that "setConfigFlags" will change config if set in manifest.json.', function () {
	global $esBlocks;
	$esBlocks = null;

	$outputCssGlobally = Components::getConfigOutputCssGlobally();
	$outputCssOptimize = Components::getConfigOutputCssOptimize();
	$outputCssSelectorName = Components::getConfigOutputCssSelectorName();

	expect($outputCssGlobally)->toBeBool()->toBeFalse();
	expect($outputCssOptimize)->toBeBool()->toBeFalse();
	expect($outputCssSelectorName)->toBeString()->toEqual('esCssVariables');

	(new BlocksExample())->getBlocksDataFullRaw();

	Components::setConfigFlags();

	$outputCssGlobally = Components::getConfigOutputCssGlobally();
	$outputCssOptimize = Components::getConfigOutputCssOptimize();
	$outputCssSelectorName = Components::getConfigOutputCssSelectorName();

	expect($outputCssGlobally)->toBeBool()->toBeTrue();
	expect($outputCssOptimize)->toBeBool()->toBeTrue();
	expect($outputCssSelectorName)->toBeString()->toEqual('esCssVariablesTest');
});

// ------------------------------------------
// getSettingsGlobalVariablesColors
// ------------------------------------------

test('Asserts that "getSettingsGlobalVariablesColors" will return global setting variables color as array.', function () {
	$result = Components::getSettingsGlobalVariablesColors();

	expect($result)->toBeArray();
});

// ------------------------------------------
// getSettingsGlobalVariablesCustomBlockName
// ------------------------------------------

test('Asserts that "getSettingsGlobalVariablesCustomBlockName" will return global setting variables custom block name.', function () {
	$result = Components::getSettingsGlobalVariablesCustomBlockName();

	expect($result)->toBeString()->toEqual('eightshift-block');
});

// ------------------------------------------
// setStyle
// ------------------------------------------

test('Asserts that "setStyle" will add style array to global store.', function () {
	$result = Components::getStyles();

	expect($result)->toBeArray()->toEqual([]);

	Components::setStyle(['test']);

	$result = Components::getStyles();

	expect($result)->toBeArray()->toEqual([['test']]);
});
