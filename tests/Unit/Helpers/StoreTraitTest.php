<?php

namespace Tests\Unit\Helpers;

use EightshiftBoilerplate\Blocks\BlocksExample;
use EightshiftLibs\Exception\InvalidBlock;
use EightshiftLibs\Helpers\Helpers;

use function Tests\buildTestBlocks;

beforeEach(function () {
	buildTestBlocks();
});

// ------------------------------------------
// getBlocks
// ------------------------------------------

test('Asserts that "getBlocks" will return empty array if blocks are missing.', function () {
	global $esBlocks;
	$esBlocks = null;

	Helpers::getBlocks();
})->throws(InvalidBlock::class, 'Trying to get missing-block block. Please check if it exists in the project.');

test('Asserts that "getBlocks" will return blocks list.', function () {
	$result = Helpers::getBlocks();

	expect($result)
		->toBeArray()
		->not->toBe([]);
});

// ------------------------------------------
// getBlock
// ------------------------------------------

test('Asserts that "getBlock" will return empty array if block is missing or wrong.', function () {
	$result = Helpers::getBlock('missing-block');

	expect($result)
		->toBeArray()
		->toBe([]);
});

test('Asserts that "getBlock" will return block manifest if name is correct.', function () {
	$result = Helpers::getBlock('button');

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

	$result = Helpers::getComponents();

	expect($result)
		->toBeArray()
		->toBe([]);
});

test('Asserts that "getComponents" will return components list.', function () {
	$result = Helpers::getComponents();

	expect($result)
		->toBeArray()
		->not->toBe([]);
});

// ------------------------------------------
// getComponent
// ------------------------------------------

test('Asserts that "getComponent" will return empty array if component is missing or wrong.', function () {
	$result = Helpers::getComponent('missing-component');

	expect($result)
		->toBeArray()
		->toBe([]);
});

test('Asserts that "getComponent" will return component manifest if name is correct.', function () {
	$result = Helpers::getComponent('button');

	expect($result)
		->toBeArray()
		->toHaveKey('componentName', 'button');
});

// ------------------------------------------
// getConfig
// ------------------------------------------

test('Asserts that "getConfig" will return config array.', function () {
	$result = Helpers::getConfig();

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

	$outputCssGlobally = Helpers::getConfigOutputCssGlobally();
	$outputCssOptimize = Helpers::getConfigOutputCssOptimize();
	$outputCssSelectorName = Helpers::getConfigOutputCssSelectorName();

	expect($outputCssGlobally)->toBeBool()->toBeFalse();
	expect($outputCssOptimize)->toBeBool()->toBeFalse();
	expect($outputCssSelectorName)->toBeString()->toEqual('esCssVariables');

	(new BlocksExample())->getBlocksDataFullRaw();

	Helpers::setConfigFlags();

	$outputCssGlobally = Helpers::getConfigOutputCssGlobally();
	$outputCssOptimize = Helpers::getConfigOutputCssOptimize();
	$outputCssSelectorName = Helpers::getConfigOutputCssSelectorName();

	expect($outputCssGlobally)->toBeBool()->toBeTrue();
	expect($outputCssOptimize)->toBeBool()->toBeTrue();
	expect($outputCssSelectorName)->toBeString()->toEqual('esCssVariablesTest');
});

// ------------------------------------------
// getSettingsGlobalVariablesColors
// ------------------------------------------

test('Asserts that "getSettingsGlobalVariablesColors" will return global setting variables color as array.', function () {
	$result = Helpers::getSettingsGlobalVariablesColors();

	expect($result)->toBeArray();
});

// ------------------------------------------
// getSettingsGlobalVariablesCustomBlockName
// ------------------------------------------

test('Asserts that "getSettingsGlobalVariablesCustomBlockName" will return global setting variables custom block name.', function () {
	$result = Helpers::getSettingsGlobalVariablesCustomBlockName();

	expect($result)->toBeString()->toEqual('eightshift-block');
});

// ------------------------------------------
// setStyle
// ------------------------------------------

test('Asserts that "setStyle" will add style array to global store.', function () {
	$result = Helpers::getStyles();

	expect($result)->toBeArray()->toEqual([]);

	Helpers::setStyle(['test']);

	$result = Helpers::getStyles();

	expect($result)->toBeArray()->toEqual([['test']]);
});
