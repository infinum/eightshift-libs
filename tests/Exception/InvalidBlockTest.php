<?php

namespace Tests\Unit\Exception;

use EightshiftLibs\Exception\InvalidBlock;

use function Tests\setupMocks;

beforeAll(function () {
	setupMocks();
});

test('Throws error if blocks are missing.', function () {

	$missingBlocks = InvalidBlock::missingBlocksException();

	$this->assertIsObject($missingBlocks);
	$this->assertObjectHasAttribute('message', $missingBlocks);
	$this->assertStringContainsString('There are no blocks added in your project.', $missingBlocks->getMessage());
});

test('Throws error if components are missing.', function () {

	$missingComponents = InvalidBlock::missingComponentsException();

	$this->assertIsObject($missingComponents);
	$this->assertObjectHasAttribute('message', $missingComponents);
	$this->assertStringContainsString('There are no components added in your project.', $missingComponents->getMessage());
});

test('Throws error if manifest key blockName is missing.', function () {

	$blockPath = 'some/random/path';
	$missingName = InvalidBlock::missingNameException($blockPath);

	$this->assertIsObject($missingName);
	$this->assertObjectHasAttribute('message', $missingName);
	$this->assertStringContainsString("Block in this path {$blockPath} is missing blockName key in its manifest.json.", $missingName->getMessage());
});

test('Throws error if block view is missing.', function () {

	$blockName = 'paragraph';
	$blockPath = 'some/random/path';
	$missingView = InvalidBlock::missingViewException($blockName, $blockPath);

	$this->assertIsObject($missingView);
	$this->assertObjectHasAttribute('message', $missingView);
	$this->assertStringContainsString("Block with this name {$blockName} is missing view template. Template name should be called {$blockName}.php, and it should be located in this path {$blockPath}", $missingView->getMessage());
});

test('Throws error if render block view is missing.', function () {

	$blockPath = 'some/random/path';
	$missingRenderView = InvalidBlock::missingRenderViewException($blockPath);

	$this->assertIsObject($missingRenderView);
	$this->assertObjectHasAttribute('message', $missingRenderView);
	$this->assertStringContainsString("Block view is missing in the provided path. Please check if {$blockPath} is the right path for your block view.", $missingRenderView->getMessage());
});

test('Throws error if global settings manifest.json is missing.', function () {

	$manifestPath = 'some/random/path';
	$missingManifestPath = InvalidBlock::missingSettingsManifestException($manifestPath);

	$this->assertIsObject($missingManifestPath);
	$this->assertObjectHasAttribute('message', $missingManifestPath);
	$this->assertStringContainsString("Global blocks settings manifest.json is missing on this location: {$manifestPath}.", $missingManifestPath->getMessage());
});

test('Throws error if wrapper settings manifest.json is missing.', function () {

	$manifestPath = 'some/random/path';
	$missingManifestPath = InvalidBlock::missingWrapperManifestException($manifestPath);

	$this->assertIsObject($missingManifestPath);
	$this->assertObjectHasAttribute('message', $missingManifestPath);
	$this->assertStringContainsString("Wrapper blocks settings manifest.json is missing on this location: {$manifestPath}.", $missingManifestPath->getMessage());
});

test('Throws error if component manifest.json is missing.', function () {

	$manifestPath = 'some/random/path';
	$missingComponentManifest = InvalidBlock::missingComponentManifestException($manifestPath);

	$this->assertIsObject($missingComponentManifest);
	$this->assertObjectHasAttribute('message', $missingComponentManifest);
	$this->assertStringContainsString("Component manifest.json is missing on this location: {$manifestPath}.", $missingComponentManifest->getMessage());
});

test('Throws error if block wrapper view is missing.', function () {

	$wrapperPath = 'some/random/path';
	$missingWrapperView = InvalidBlock::missingWrapperViewException($wrapperPath);

	$this->assertIsObject($missingWrapperView);
	$this->assertObjectHasAttribute('message', $missingWrapperView);
	$this->assertStringContainsString("Wrapper view is missing. Template should be located in this path {$wrapperPath}", $missingWrapperView->getMessage());
});

test('Throws error if global manifest settings key namespace is missing.', function () {

	$missingNamespace = InvalidBlock::missingNamespaceException();

	$this->assertIsObject($missingNamespace);
	$this->assertObjectHasAttribute('message', $missingNamespace);
	$this->assertStringContainsString('Global Blocks settings manifest.json is missing a key called namespace. This key prefixes all block names.', $missingNamespace->getMessage());
});
