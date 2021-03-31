<?php

namespace Tests\Unit\Exception;

use EightshiftLibs\Exception\InvalidBlock;

use Brain\Monkey;

use function Tests\setupMocks;

beforeAll(function () {
	Monkey\setUp();
	setupMocks();
});

afterAll(function() {
	Monkey\tearDown();
});

test('Checks if missingBlocksException will return correct response.', function () {

	$missingBlocks = InvalidBlock::missingBlocksException();

	$this->assertIsObject($missingBlocks, "The {$missingBlocks} should be an instance of InvalidBlock class");
	$this->assertObjectHasAttribute('message', $missingBlocks, "Object doesn't contain message attribute");
	$this->assertStringContainsString('There are no blocks added in your project.', $missingBlocks->getMessage(), "Strings for message if there are no blocks added to the project do not match!");
});

test('Checks if missingComponentsException will return correct response.', function () {

	$missingComponents = InvalidBlock::missingComponentsException();

	$this->assertIsObject($missingComponents);
	$this->assertObjectHasAttribute('message', $missingComponents);
	$this->assertStringContainsString('There are no components added in your project.', $missingComponents->getMessage());
});

test('Checks if missingNameException will return correct response.', function () {

	$blockPath = 'some/random/path';
	$missingName = InvalidBlock::missingNameException($blockPath);

	$this->assertIsObject($missingName);
	$this->assertObjectHasAttribute('message', $missingName);
	$this->assertStringContainsString("Block in this path {$blockPath} is missing blockName key in its manifest.json.", $missingName->getMessage());
});

test('Checks if missingViewException will return correct response.', function () {

	$blockName = 'paragraph';
	$blockPath = 'some/random/path';
	$missingView = InvalidBlock::missingViewException($blockName, $blockPath);

	$this->assertIsObject($missingView);
	$this->assertObjectHasAttribute('message', $missingView);
	$this->assertStringContainsString("Block with this name {$blockName} is missing view template. Template name should be called {$blockName}.php, and it should be located in this path {$blockPath}", $missingView->getMessage());
});

test('Checks if missingRenderViewException will return correct response.', function () {

	$blockPath = 'some/random/path';
	$missingRenderView = InvalidBlock::missingRenderViewException($blockPath);

	$this->assertIsObject($missingRenderView);
	$this->assertObjectHasAttribute('message', $missingRenderView);
	$this->assertStringContainsString("Block view is missing in the provided path. Please check if {$blockPath} is the right path for your block view.", $missingRenderView->getMessage());
});

test('Checks if missingSettingsManifestException will return correct response.', function () {

	$manifestPath = 'some/random/path';
	$missingManifestPath = InvalidBlock::missingSettingsManifestException($manifestPath);

	$this->assertIsObject($missingManifestPath);
	$this->assertObjectHasAttribute('message', $missingManifestPath);
	$this->assertStringContainsString("Global blocks settings manifest.json is missing on this location: {$manifestPath}.", $missingManifestPath->getMessage());
});

test('Checks if missingWrapperManifestException will return correct response.', function () {

	$manifestPath = 'some/random/path';
	$missingManifestPath = InvalidBlock::missingWrapperManifestException($manifestPath);

	$this->assertIsObject($missingManifestPath);
	$this->assertObjectHasAttribute('message', $missingManifestPath);
	$this->assertStringContainsString("Wrapper blocks settings manifest.json is missing on this location: {$manifestPath}.", $missingManifestPath->getMessage());
});

test('Checks if missingComponentManifestException will return correct response.', function () {

	$manifestPath = 'some/random/path';
	$missingComponentManifest = InvalidBlock::missingComponentManifestException($manifestPath);

	$this->assertIsObject($missingComponentManifest);
	$this->assertObjectHasAttribute('message', $missingComponentManifest);
	$this->assertStringContainsString("Component manifest.json is missing on this location: {$manifestPath}.", $missingComponentManifest->getMessage());
});

test('Checks if missingWrapperViewException will return correct response.', function () {

	$wrapperPath = 'some/random/path';
	$missingWrapperView = InvalidBlock::missingWrapperViewException($wrapperPath);

	$this->assertIsObject($missingWrapperView);
	$this->assertObjectHasAttribute('message', $missingWrapperView);
	$this->assertStringContainsString("Wrapper view is missing. Template should be located in this path {$wrapperPath}", $missingWrapperView->getMessage());
});

test('Checks if missingNamespaceException will return correct response.', function () {

	$missingNamespace = InvalidBlock::missingNamespaceException();

	$this->assertIsObject($missingNamespace);
	$this->assertObjectHasAttribute('message', $missingNamespace);
	$this->assertStringContainsString('Global Blocks settings manifest.json is missing a key called namespace. This key prefixes all block names.', $missingNamespace->getMessage());
});
