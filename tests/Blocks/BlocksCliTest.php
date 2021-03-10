<?php

namespace Tests\Unit\Blocks;

use EightshiftLibs\Blocks\BlocksCli;
use function Tests\deleteCliOutput;

/**
 * Mock before tests.
 */
beforeEach(function () {
	$wpCliMock = \Mockery::mock('alias:WP_CLI');

$wpCliMock
	->shouldReceive('success')
	->andReturnArg(0);

$wpCliMock
	->shouldReceive('error')
	->andReturnArg(0);

$this->blocks = new BlocksCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	$output = dirname(__FILE__, 3) . '/cliOutput';

	deleteCliOutput($output);
});

test('Blocks CLI command will correctly copy the Blocks class with defaults', function () {
	$blocks = $this->blocks;
	$blocks([], []);

	$outputPath = dirname(__FILE__, 3) . '/cliOutput/src/Blocks/Blocks.php';

	// Check the output dir if the generated method is correctly generated.
	$generatedBlocks = file_get_contents($outputPath);

	$this->assertStringContainsString('class Blocks extends AbstractBlocks', $generatedBlocks);
	$this->assertStringContainsString('@package EightshiftBoilerplate\Blocks', $generatedBlocks);
	$this->assertStringContainsString('namespace EightshiftLibs\Blocks', $generatedBlocks);
	$this->assertStringNotContainsString('footer.php', $generatedBlocks);
	$this->assertFileExists($outputPath);
});

test('Blocks CLI command will correctly copy the Blocks class with set arguments', function () {
	$blocks = $this->blocks;
	$blocks([], [
		'namespace' => 'CoolTheme',
	]);

	// Check the output dir if the generated method is correctly generated.
	$generatedBlocks = file_get_contents(dirname(__FILE__, 3) . '/cliOutput/src/Blocks/Blocks.php');

	$this->assertStringContainsString('namespace CoolTheme\Blocks;', $generatedBlocks);
});

test('Blocks CLI documentation is correct', function () {
	$blocks = $this->blocks;

	$documentation = $blocks->getDoc();

	$key = 'shortdesc';

	$this->assertIsArray($documentation);
	$this->assertArrayHasKey($key, $documentation);
	$this->assertArrayNotHasKey('synopsis', $documentation);
	$this->assertEquals('Generates Blocks class.', $documentation[$key]);
});
