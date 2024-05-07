<?php

namespace Tests\Unit\Helpers;

use Infinum\Blocks\BlocksExample;
use EightshiftLibs\Exception\InvalidBlock;
use EightshiftLibs\Helpers\Helpers;

use function Tests\buildTestBlocks;

beforeEach(function () {
	buildTestBlocks();
});

// ------------------------------------------
// getBlocks
// ------------------------------------------

test('Asserts that "getBlocks" will throw and error if blocks are missing.', function () {
	global $esBlocks;
	$esBlocks = null;

	Helpers::getBlocks();
})->throws(InvalidBlock::class, 'Trying to get project blocks. Please check if it exists in the project.');

test('Asserts that "getBlocks" will return blocks list.', function () {
	expect(Helpers::getBlocks())
		->toBeArray()
		->not->toBe([]);
});

// ------------------------------------------
// getBlock
// ------------------------------------------

test('Asserts that "getBlock" will throw and error if block is missing or wrong.', function () {
	Helpers::getBlock('missing-block');
})->throws(InvalidBlock::class, 'Trying to get missing-block block. Please check if it exists in the project.');

test('Asserts that "getBlock" will return block manifest if name is correct.', function () {
	expect(Helpers::getBlock('button'))
		->toBeArray()
		->toHaveKey('blockName', 'button');
});

// ------------------------------------------
// getComponents
// ------------------------------------------

test('Asserts that "getComponents" will throw and error if components are missing.', function () {
	global $esBlocks;
	$esBlocks = null;

	Helpers::getComponents();

})->throws(InvalidBlock::class, 'Trying to get project components. Please check if it exists in the project.');

test('Asserts that "getComponents" will return components list.', function () {
	expect(Helpers::getComponents())
		->toBeArray()
		->not->toBe([]);
});

// ------------------------------------------
// getComponent
// ------------------------------------------

test('Asserts that "getComponent" will throw an error if component is missing or wrong.', function () {
	Helpers::getComponent('missing-component');

})->throws(InvalidBlock::class, 'Trying to get missing-component component. Please check if it exists in the project.');

test('Asserts that "getComponent" will return component manifest if name is correct.', function () {
	expect(Helpers::getComponent('button'))
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

	buildTestBlocks();

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
