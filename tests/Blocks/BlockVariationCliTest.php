<?php

namespace Tests\Unit\Block;

use EightshiftLibs\Blocks\BlockVariationCli;

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

$this->variation = new BlockVariationCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	$output = dirname(__FILE__, 3) . '/cliOutput';

	deleteCliOutput($output);
});

 test('Variation CLI command will correctly copy the Variation class with defaults', function () {
	$variationMock = mock(BlockVariationCli::class)
		->makePartial()
		->shouldReceive('getFrontendLibsBlockPath')
		->andReturn(dirname(__FILE__, 2) . '/data');

	$mock = $variationMock->getMock();

	$mock([], [$this->variation->getDevelopArgs([])]);

	$outputPath = dirname(__FILE__, 3) . '/cliOutput/button-block/manifest.json';

	// Check the output dir if the generated method is correctly generated.
	$generatedVariation = file_get_contents($outputPath);

	$this->assertStringContainsString('"parentName": "button"', $generatedVariation);
	$this->assertFileExists($outputPath);
 });

 test('Variation CLI command will run under custom command name', function () {
	$variation = $this->variation;
	$result = $variation->getCommandName();

	$this->assertStringContainsString('use_variation', $result);
});

test('Variation CLI documentation is correct', function () {
	$variation = $this->variation;

	$documentation = $variation->getDoc();

	$key = 'shortdesc';

	$this->assertIsArray($documentation);
	$this->assertArrayHasKey($key, $documentation);
	$this->assertArrayHasKey('synopsis', $documentation);
	$this->assertEquals('Copy Variation from library to your project.', $documentation[$key]);
});

test('Variation CLI command will fail if Variation doesn\'t exist', function () {
	$variationMock = mock(BlockVariationCli::class)
		->makePartial()
		->shouldReceive('getFrontendLibsBlockPath')
		->andReturn(dirname(__FILE__, 2) . '/data');

	$mock = $variationMock->getMock();

	$mock([], ['name' => 'ivan']);

	$outputPath = dirname(__FILE__, 3) . '/cliOutput/ivan/ivan.php';

	$this->assertFileDoesNotExist($outputPath);
});
