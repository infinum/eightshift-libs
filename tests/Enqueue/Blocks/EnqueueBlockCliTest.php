<?php

namespace Tests\Unit\EnqueueBlock;

use EightshiftLibs\Enqueue\Blocks\EnqueueBlocksCli;

use function Tests\deleteCliOutput;
use function Tests\mock;

/**
 * Mock before tests.
 */
beforeEach(function () {
	$wpCliMock = mock('alias:WP_CLI');

	$wpCliMock
		->shouldReceive('success')
		->andReturnArg(0);

	$wpCliMock
		->shouldReceive('error')
		->andReturnArg(0);

	$this->ebc = new EnqueueBlocksCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	deleteCliOutput(\dirname(__FILE__, 4) . '/cliOutput');
});

/**
 * Making an appropriate class with all it's key strings.
 */
test('Enqueue Block CLI command will make appropriate class.', function () {
	$ebc = $this->ebc;
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
	$this->assertStringContainsString('enqueueBlockScript', $generatedEBC);
});

/**
 * Testing if correct namespace will be set.
 */
test('Enqueue Block CLI command will set correct namespace.', function () {
	$ebc = $this->ebc;
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
	$ebc = $this->ebc;
	$ebc([], []);

	$generatedEBC = \file_get_contents(\dirname(__FILE__, 4) . '/cliOutput/src/Enqueue/Blocks/EnqueueBlocks.php');

	$this->assertStringContainsString('getAssetsPrefix', $generatedEBC);
	$this->assertStringContainsString('getAssetsVersion', $generatedEBC);
});

test('Custom Enqueue Blocks CLI documentation is correct', function () {
	$ebc = $this->ebc;
	$documentation = $ebc->getDoc();
	$descKey = 'shortdesc';

	$this->assertIsArray($documentation, 'Returned value must be an array.');
	$this->assertArrayHasKey($descKey, $documentation, 'Array doesn\'t have a required key.');
	$this->assertSame('Generates Enqueue Blocks class.', $documentation[$descKey], 'Returned string doesn\'t match');
});
