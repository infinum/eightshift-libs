<?php

namespace Tests\Unit\Block;

use EightshiftLibs\Blocks\BlockCli;

use EightshiftLibs\Exception\InvalidBlock;
use Exception;

use function Tests\mock;
use function Tests\setAfterEach;
use function Tests\setBeforeEach;

/**
 * Mock before tests.
 */
beforeEach(function () {
	setBeforeEach();

	$this->block = new BlockCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	setAfterEach();

	unset($this->block);
});

// test('Block CLI command will correctly copy the Block class with defaults', function () {
// 	$blockMock = mock(BlockCli::class)
// 		->makePartial()
// 		->shouldReceive('getFrontendLibsBlockPath')
// 		->andReturn(\dirname(__FILE__, 2) . '/data');

// 	$mock = $blockMock->getMock();

// 	$mock([], [$this->block->getDevelopArgs([])]);

// 	$outputPath = \dirname(__FILE__, 3) . '/cliOutput/button/button.php';

// 	// Check the output dir if the generated method is correctly generated.
// 	$generatedBlock = \file_get_contents($outputPath);

// 	$this->assertStringContainsString('Template for the Button Block view.', $generatedBlock);
// 	$this->assertStringContainsString('@package EightshiftBoilerplate', $generatedBlock);
// 	$this->assertStringNotContainsString('Components::render(\'link\', $attributes)', $generatedBlock);
// 	$this->assertFileExists($outputPath);
//  });

// test('Block CLI command will run under custom command name', function () {
// 	$block = $this->block;
// 	$result = $block->getCommandName();

// 	expect($result)->toContain('block');
// });

// test('Block CLI documentation is correct', function () {
// 	expect($this->block->getDoc())->toBeArray();
// });

// test('Block CLI command will fail if block doesn\'t exist', function () {
// 	$blockMock = mock(BlockCli::class)->makePartial();
// 	$blockMock->shouldReceive('getFrontendLibsBlockPath')
// 		->andReturn(\dirname(__FILE__, 2) . '/data');

// 		// var_dump($this->block->__invoke([], ['name' => 'testing']));

// 	// $mock = $blockMock->getMock();
// 	$a = $blockMock->__invoke([], ['name' => 'testing']);

// 	var_dump($a);

// 	// $mock([], ['name' => 'testing']);
// })->throws(InvalidBlock::class);

test('Block CLI command will fail if block doesn\'t exist', function () {
	$blockMock = mock(BlockCli::class)->makePartial();
	$blockMock->shouldReceive('getFrontendLibsBlockPath')->andReturn(\dirname(__FILE__, 2) . '/data');
	$blockMock->shouldReceive('__invoke')->andReturn(false);

	$a = $blockMock->commandAction([], ['name' => 'testing']);

	var_dump($a);

	// expect($a)->toThrow(InvalidBlock::class);
	// expectException(InvalidBlock::class);

});
// })->expectException(InvalidBlock::class);
