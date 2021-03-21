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

$this->block = new BlockCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	$output = dirname(__FILE__, 3) . '/cliOutput';

	deleteCliOutput($output);
});

// test('Block CLI command will correctly copy the Block class with defaults', function () {
// 	$block = $this->block;
// 	$block([], $block->getDevelopArgs([]));

// 	$a = $this->getMock('Block');
// 	$a->expects($this->any())->method('getProjectRootPath')->will($this->returnValue('ivan'));
// 	$a->expects($this->any())->method('getFrontendLibsBlockPath')->will($this->returnValue('novo'));

// 	// $outputPath = dirname(__FILE__, 3) . '/cliOutput/src/Block/Block.php';


// 	// Check the output dir if the generated method is correctly generated.
// 	// $generatedBlock = file_get_contents($outputPath);

// 	// $this->assertStringContainsString('class Block extends AbstractBlock', $generatedBlock);
// 	// $this->assertStringContainsString('@package EightshiftBoilerplate\Block', $generatedBlock);
// 	// $this->assertStringContainsString('namespace EightshiftLibs\Block', $generatedBlock);
// 	// $this->assertStringNotContainsString('footer.php', $generatedBlock);
// 	// $this->assertFileExists($outputPath);
// });

// test('Block CLI command will correctly copy the Block class with set arguments', function () {
// 	$block = $this->block;
// 	$block([], [
// 		'namespace' => 'CoolTheme',
// 	]);

// 	// Check the output dir if the generated method is correctly generated.
// 	$generatedBlock = file_get_contents(dirname(__FILE__, 3) . '/cliOutput/src/Block/Block.php');

// 	$this->assertStringContainsString('namespace CoolTheme\Block;', $generatedBlock);
// });

test('Block CLI documentation is correct', function () {
	$block = $this->block;

	$documentation = $block->getDoc();

	$key = 'shortdesc';

	$this->assertIsArray($documentation);
	$this->assertArrayHasKey($key, $documentation);
	$this->assertArrayHasKey('synopsis', $documentation);
	$this->assertEquals('Copy Block from library to your project.', $documentation[$key]);
});
