<?php

namespace Tests\Unit\EnqueueBlock;

use EightshiftLibs\Enqueue\Blocks\EnqueueBlocksCli;
use EightshiftLibs\Helpers\Components;

beforeEach(function () {
	$this->mock = new EnqueueBlocksCli('boilerplate');
});

afterEach(function () {
	unset($this->mock);
});

/**
 * Making an appropriate class with all it's key strings.
 */
test('Enqueue Block CLI command will make appropriate class.', function () {
	$ebc = $this->mock;
	$ebc([], []);

	$generatedEBC = \file_get_contents(Components::getProjectPaths('srcDestination', 'Enqueue/Blocks/EnqueueBlocks.php'));

	expect($generatedEBC)
		->toContain('class EnqueueBlocks extends AbstractEnqueueBlocks')
		->toContain('enqueue_block_editor_assets')
		->toContain('enqueueBlockEditorScript')
		->toContain('enqueue_block_editor_assets')
		->toContain('enqueueBlockEditorStyle')
		->toContain('enqueue_block_assets')
		->toContain('enqueueBlockStyle')
		->toContain('wp_enqueue_scripts')
		->toContain('enqueueBlockFrontendScript')
		->toContain('wp_enqueue_scripts');
});

/**
 * Testing if correct namespace will be set.
 */
test('Enqueue Block CLI command will set correct namespace.', function () {
	$ebc = $this->mock;
	$ebc([],[
		'namespace' => 'NewTheme',
	]);

	$generatedEBC = \file_get_contents(Components::getProjectPaths('srcDestination', 'Enqueue/Blocks/EnqueueBlocks.php'));

	expect($generatedEBC)->toContain('namespace NewTheme\Enqueue\Blocks;');
});

/**
 * Testing if correct functions will be generated.
 */
test('Enqueue Block CLI command will set correct functions.', function () {
	$ebc = $this->mock;
	$ebc([], []);

	$generatedEBC = \file_get_contents(Components::getProjectPaths('srcDestination', 'Enqueue/Blocks/EnqueueBlocks.php'));

	expect($generatedEBC)
		->toContain('getAssetsPrefix')
		->toContain('getAssetsVersion');
});

test('Custom Enqueue Blocks CLI documentation is correct', function () {
	expect($this->mock->getDoc())->toBeArray();
});
