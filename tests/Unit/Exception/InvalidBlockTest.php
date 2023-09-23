<?php

namespace Tests\Unit\Exception;

use EightshiftLibs\Exception\InvalidBlock;

test('Checks if missingBlocksException will return correct response.', function () {

	$missingBlocks = InvalidBlock::missingBlocksException();

	expect($missingBlocks)->toBeObject()
		->toBeInstanceOf(InvalidBlock::class)
		->toHaveProperty('message')
		->and('There are no blocks added in your project.')
		->toEqual($missingBlocks->getMessage());
});

test('Checks if missingComponentsException will return correct response.', function () {

	$missingComponents = InvalidBlock::missingComponentsException();

	expect($missingComponents)->toBeObject()
		->toBeInstanceOf(InvalidBlock::class)
		->toHaveProperty('message')
		->and('There are no components added in your project.')
		->toEqual($missingComponents->getMessage());
});

test('Checks if missingNameException will return correct response.', function () {

	$blockPath = 'some/random/path';
	$missingName = InvalidBlock::missingNameException($blockPath);

	expect($missingName)->toBeObject()
		->toBeInstanceOf(InvalidBlock::class)
		->toHaveProperty('message')
		->and("Block in this path {$blockPath} is missing blockName key in its manifest.json.")
		->toEqual($missingName->getMessage());
});

test('Checks if missingViewException will return correct response.', function () {

	$blockName = 'paragraph';
	$blockPath = 'some/random/path';
	$missingView = InvalidBlock::missingViewException($blockName, $blockPath);

	expect($missingView)->toBeObject()
		->toBeInstanceOf(InvalidBlock::class)
		->toHaveProperty('message')
		->and("Block with this name {$blockName} is missing view template. Template name should be called {$blockName}.php, and it should be located in this path {$blockPath}")
		->toEqual($missingView->getMessage());
});

test('Checks if missingRenderViewException will return correct response.', function () {

	$blockPath = 'some/random/path';
	$missingRenderView = InvalidBlock::missingRenderViewException($blockPath);

	expect($missingRenderView)->toBeObject()
		->toBeInstanceOf(InvalidBlock::class)
		->toHaveProperty('message')
		->and("Block view is missing in the provided path. Please check if {$blockPath} is the right path for your block view.")
		->toEqual($missingRenderView->getMessage());
});

test('Checks if missingSettingsManifestException will return correct response.', function () {

	$manifestPath = 'some/random/path';
	$missingManifestPath = InvalidBlock::missingSettingsManifestException($manifestPath);

	expect($missingManifestPath)->toBeObject()
		->toBeInstanceOf(InvalidBlock::class)
		->toHaveProperty('message')
		->and("Global blocks settings manifest.json is missing on this location: {$manifestPath}.")
		->toEqual($missingManifestPath->getMessage());
});

test('Checks if missingWrapperManifestException will return correct response.', function () {

	$manifestPath = 'some/random/path';
	$missingManifestPath = InvalidBlock::missingWrapperManifestException($manifestPath);

	expect($missingManifestPath)->toBeObject()
		->toBeInstanceOf(InvalidBlock::class)
		->toHaveProperty('message')
		->and("Wrapper blocks settings manifest.json is missing on this location: {$manifestPath}.")
		->toEqual($missingManifestPath->getMessage());
});

test('Checks if missingComponentManifestException will return correct response.', function () {

	$manifestPath = 'some/random/path';
	$missingComponentManifest = InvalidBlock::missingComponentManifestException($manifestPath);

	expect($missingComponentManifest)->toBeObject()
		->toBeInstanceOf(InvalidBlock::class)
		->toHaveProperty('message')
		->and("Component manifest.json is missing on this location: {$manifestPath}.")
		->toEqual($missingComponentManifest->getMessage());
});

test('Checks if missingWrapperViewException will return correct response.', function () {

	$wrapperPath = 'some/random/path';
	$missingWrapperView = InvalidBlock::missingWrapperViewException($wrapperPath);

	expect($missingWrapperView)->toBeObject()
		->toBeInstanceOf(InvalidBlock::class)
		->toHaveProperty('message')
		->and("Wrapper view is missing. Template should be located in this path {$wrapperPath}")
		->toEqual($missingWrapperView->getMessage());
});

test('Checks if missingNamespaceException will return correct response.', function () {

	$missingNamespace = InvalidBlock::missingNamespaceException();

	expect($missingNamespace)->toBeObject()
		->toBeInstanceOf(InvalidBlock::class)
		->toHaveProperty('message')
		->and('Global Blocks settings manifest.json is missing a key called namespace. This key prefixes all block names.')
		->toEqual($missingNamespace->getMessage());
});
