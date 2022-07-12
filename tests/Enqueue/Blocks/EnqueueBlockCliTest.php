<?php

namespace Tests\Unit\EnqueueBlock;

use EightshiftLibs\Enqueue\Blocks\EnqueueBlocksCli;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->mock = new EnqueueBlocksCli('boilerplate');
});

afterEach(function () {
	setAfterEach();

	unset($this->mock);
});

/**
 * Making an appropriate class with all it's key strings.
 */
test('Enqueue Block CLI command will make appropriate class.', function () {
	$ebc = $this->mock;
	$ebc([], []);

	$generatedEBC = \file_get_contents(\dirname(__FILE__, 4) . '/cliOutput/src/Enqueue/Blocks/EnqueueBlocks.php');
	$this->assertStringContainsString('class EnqueueBlocks extends AbstractEnqueueBlocks', $generatedEBC);

	$this->assertStringContainsString('enqueue_block_editor_assets', $generatedEBC);
	$this->assertStringContainsString('enqueueBlockEditorScript', $generatedEBC);

	$this->assertStringContainsString('enqueue_block_editor_assets', $generatedEBC);
	$this->assertStringContainsString('enqueueBlockEditorStyle', $generatedEBC);

	$this->assertStringContainsString('enqueue_block_assets', $generatedEBC);
	$this->assertStringContainsString('enqueueBlockStyle', $generatedEBC);

	$this->assertStringContainsString('wp_enqueue_scripts', $generatedEBC);
	$this->assertStringContainsString('enqueueBlockFrontendScript', $generatedEBC);

	$this->assertStringContainsString('wp_enqueue_scripts', $generatedEBC);
	$this->assertStringContainsString('enqueueBlockFrontendStyle', $generatedEBC);
});

/**
 * Testing if correct namespace will be set.
 */
test('Enqueue Block CLI command will set correct namespace.', function () {
	$ebc = $this->mock;
	$ebc([],[
		'namespace' => 'NewTheme',
	]);

	$generatedEBC = \file_get_contents(\dirname(__FILE__, 4) . '/cliOutput/src/Enqueue/Blocks/EnqueueBlocks.php');

	$this->assertStringContainsString('namespace NewTheme\Enqueue\Blocks;', $generatedEBC);
});

/**
 * Testing if correct functions will be generated.
 */
test('Enqueue Block CLI command will set correct functions.', function () {
	$ebc = $this->mock;
	$ebc([], []);

	$generatedEBC = \file_get_contents(\dirname(__FILE__, 4) . '/cliOutput/src/Enqueue/Blocks/EnqueueBlocks.php');

	$this->assertStringContainsString('getAssetsPrefix', $generatedEBC);
	$this->assertStringContainsString('getAssetsVersion', $generatedEBC);
});

test('Custom Enqueue Blocks CLI documentation is correct', function () {
	expect($this->mock->getDoc())->toBeArray();
});
