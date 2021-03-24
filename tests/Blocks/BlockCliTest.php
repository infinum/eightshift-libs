<?php

namespace Tests\Unit\Block;

use EightshiftLibs\Blocks\BlockCli;

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

$wpCliMock
	->shouldReceive('log')
	->andReturnArg(0);

$this->block = new BlockCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	$output = dirname(__FILE__, 3) . '/cliOutput';

	deleteCliOutput($output);
});

 test('Block CLI command will correctly copy the Block class with defaults', function () {
	$blockMock = mock(BlockCli::class)
		->makePartial()
		->shouldReceive('getFrontendLibsBlockPath')
		->andReturn(dirname(__FILE__, 2) . '/data/frontend-libs');

	$mock = $blockMock->getMock();

	$mock([], []);

	$outputPath = dirname(__FILE__, 3) . '/cliOutput/button/button.php';

	//Check the output dir if the generated method is correctly generated.
	$generatedBlock = file_get_contents($outputPath);

	$this->assertStringContainsString('Template for the Button Block view.', $generatedBlock);
	$this->assertStringContainsString('@package EightshiftBoilerplate', $generatedBlock);
	$this->assertStringNotContainsString('Components::render(\'link\', $attributes)', $generatedBlock);
	$this->assertFileExists($outputPath);
 });


test('Block CLI documentation is correct', function () {
	$block = $this->block;

	$documentation = $block->getDoc();

	$key = 'shortdesc';

	$this->assertIsArray($documentation);
	$this->assertArrayHasKey($key, $documentation);
	$this->assertArrayHasKey('synopsis', $documentation);
	$this->assertEquals('Copy Block from library to your project.', $documentation[$key]);
});
