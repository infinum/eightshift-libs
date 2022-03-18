<?php

namespace Tests\Unit\Block;

use EightshiftLibs\Blocks\BlockCli;

use EightshiftLibs\Exception\InvalidBlock;

use function Tests\deleteCliOutput;
use function Tests\mock;
use function Tests\setupMocks;

/**
 * Mock before tests.
 */
beforeEach(function () {
	setupMocks();

	$wpCliMock = mock('alias:WP_CLI');

	$wpCliMock
		->shouldReceive('success')
		->andReturnArg(0);

	$wpCliMock
		->shouldReceive('error')
		->andReturnArg(0);

	$wpCliMock
		->shouldReceive('log')
		->andReturnArg(0);

	$this->block = new BlockCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	$output = \dirname(__FILE__, 3) . '/cliOutput';

	deleteCliOutput($output);
});

 test('Block CLI command will correctly copy the Block class with defaults', function () {
	$blockMock = mock(BlockCli::class)
		->makePartial()
		->shouldReceive('getFrontendLibsBlockPath')
		->andReturn(\dirname(__FILE__, 2) . '/data');

	$mock = $blockMock->getMock();

	$mock([], [$this->block->getDevelopArgs([])]);

	$outputPath = \dirname(__FILE__, 3) . '/cliOutput/button/button.php';

	// Check the output dir if the generated method is correctly generated.
	$generatedBlock = \file_get_contents($outputPath);

	$this->assertStringContainsString('Template for the Button Block view.', $generatedBlock);
	$this->assertStringContainsString('@package EightshiftBoilerplate', $generatedBlock);
	$this->assertStringNotContainsString('Components::render(\'link\', $attributes)', $generatedBlock);
	$this->assertFileExists($outputPath);
 });

test('Block CLI command will run under custom command name', function () {
	$block = $this->block;
	$result = $block->getCommandName();

	$this->assertStringContainsString('use_block', $result);
});

test('Block CLI documentation is correct', function () {
	$block = $this->block;

	$documentation = $block->getDoc();

	$key = 'shortdesc';

	$this->assertIsArray($documentation);
	$this->assertArrayHasKey($key, $documentation);
	$this->assertArrayHasKey('synopsis', $documentation);
	$this->assertSame('Copy Block from library to your project.', $documentation[$key]);
});

test('Block CLI command will fail if block doesn\'t exist', function () {
	$blockMock = mock(BlockCli::class)
		->makePartial()
		->shouldReceive('getFrontendLibsBlockPath')
		->andReturn(\dirname(__FILE__, 2) . '/data');

	$mock = $blockMock->getMock();

	$mock([], ['name' => 'testing']);
})->expectException(InvalidBlock::class);
