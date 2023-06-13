<?php

namespace Tests\Unit\Exception;

use EightshiftLibs\Exception\InvalidBlock;

test('Checks if missingBlocksException will return correct response.', function () {

	$missingBlocks = InvalidBlock::missingBlocksException();

	$this->assertIsObject($missingBlocks, "The {$missingBlocks} should be an instance of InvalidBlock class");
	$this->assertObjectHasProperty('message', $missingBlocks, "Object doesn't contain message attribute");
	$this->assertSame('There are no blocks added in your project.', $missingBlocks->getMessage(), "Strings for message if there are no blocks added to the project do not match!");
});

test('Checks if missingComponentsException will return correct response.', function () {

	$missingComponents = InvalidBlock::missingComponentsException();

	$this->assertIsObject($missingComponents);
	$this->assertObjectHasProperty('message', $missingComponents);
	$this->assertSame('There are no components added in your project.', $missingComponents->getMessage(), "Strings for message if there are no components added to the project do not match!");
});

test('Checks if missingNameException will return correct response.', function () {

	$blockPath = 'some/random/path';
	$missingName = InvalidBlock::missingNameException($blockPath);

	$this->assertIsObject($missingName);
	$this->assertObjectHasProperty('message', $missingName);
	$this->assertSame("Block in this path {$blockPath} is missing blockName key in its manifest.json.", $missingName->getMessage(), "Strings for message if blockName key is missing in manifest.json do not match!");
});

test('Checks if missingViewException will return correct response.', function () {

	$blockName = 'paragraph';
	$blockPath = 'some/random/path';
	$missingView = InvalidBlock::missingViewException($blockName, $blockPath);

	$this->assertIsObject($missingView);
	$this->assertObjectHasProperty('message', $missingView);
	$this->assertSame("Block with this name {$blockName} is missing view template. Template name should be called {$blockName}.php, and it should be located in this path {$blockPath}", $missingView->getMessage(), "Strings for message if block is missing view template do not match!");
});

test('Checks if missingRenderViewException will return correct response.', function () {

	$blockPath = 'some/random/path';
	$missingRenderView = InvalidBlock::missingRenderViewException($blockPath);

	$this->assertIsObject($missingRenderView);
	$this->assertObjectHasProperty('message', $missingRenderView);
	$this->assertSame("Block view is missing in the provided path. Please check if {$blockPath} is the right path for your block view.", $missingRenderView->getMessage(), "Strings for message if block view is missing provided path do not match!");
});

test('Checks if missingSettingsManifestException will return correct response.', function () {

	$manifestPath = 'some/random/path';
	$missingManifestPath = InvalidBlock::missingSettingsManifestException($manifestPath);

	$this->assertIsObject($missingManifestPath);
	$this->assertObjectHasProperty('message', $missingManifestPath);
	$this->assertSame("Global blocks settings manifest.json is missing on this location: {$manifestPath}.", $missingManifestPath->getMessage(), "Strings for message if global blocks settings manifest.json is missing do not match!");
});

test('Checks if missingWrapperManifestException will return correct response.', function () {

	$manifestPath = 'some/random/path';
	$missingManifestPath = InvalidBlock::missingWrapperManifestException($manifestPath);

	$this->assertIsObject($missingManifestPath);
	$this->assertObjectHasProperty('message', $missingManifestPath);
	$this->assertSame("Wrapper blocks settings manifest.json is missing on this location: {$manifestPath}.", $missingManifestPath->getMessage(), "Strings for message if wrapper blocks settings manifest.json is missing do not match!");
});

test('Checks if missingComponentManifestException will return correct response.', function () {

	$manifestPath = 'some/random/path';
	$missingComponentManifest = InvalidBlock::missingComponentManifestException($manifestPath);

	$this->assertIsObject($missingComponentManifest);
	$this->assertObjectHasProperty('message', $missingComponentManifest);
	$this->assertSame("Component manifest.json is missing on this location: {$manifestPath}.", $missingComponentManifest->getMessage(), "Strings for message if component manifest.json is missing do not match!");
});

test('Checks if missingWrapperViewException will return correct response.', function () {

	$wrapperPath = 'some/random/path';
	$missingWrapperView = InvalidBlock::missingWrapperViewException($wrapperPath);

	$this->assertIsObject($missingWrapperView);
	$this->assertObjectHasProperty('message', $missingWrapperView);
	$this->assertSame("Wrapper view is missing. Template should be located in this path {$wrapperPath}", $missingWrapperView->getMessage(), "Strings for message if wrapper view is missing do not match!");
});

test('Checks if missingNamespaceException will return correct response.', function () {

	$missingNamespace = InvalidBlock::missingNamespaceException();

	$this->assertIsObject($missingNamespace);
	$this->assertObjectHasProperty('message', $missingNamespace);
	$this->assertSame('Global Blocks settings manifest.json is missing a key called namespace. This key prefixes all block names.', $missingNamespace->getMessage(), "Strings for message global settings manifest.json is missing a key called namespace do not match!");
});
