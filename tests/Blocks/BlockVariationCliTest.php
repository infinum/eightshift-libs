<?php

namespace Tests\Unit\Block;

use EightshiftLibs\Blocks\BlockVariationCli;

use EightshiftLibs\Exception\InvalidBlock;

use function Tests\mock;
use function Tests\setAfterEach;
use function Tests\setBeforeEach;

// /**
//  * Mock before tests.
//  */
// beforeEach(function () {
// 	setBeforeEach();

// 	$this->variation = new BlockVariationCli('boilerplate');
// });

// /**
//  * Cleanup after tests.
//  */
// afterEach(function () {
// 	setAfterEach();
// });

//  test('Variation CLI command will correctly copy the variation class with defaults', function () {
// 	$variationMock = mock(BlockVariationCli::class)
// 		->makePartial()
// 		->shouldReceive('getFrontendLibsBlockPath')
// 		->andReturn(\dirname(__FILE__, 2) . '/data');

// 	$mock = $variationMock->getMock();

// 	$mock([], [$this->variation->getDevelopArgs([])]);

// 	$outputPath = \dirname(__FILE__, 3) . '/cliOutput/button-block/manifest.json';

// 	// Check the output dir if the generated method is correctly generated.
// 	$generatedVariation = \file_get_contents($outputPath);

// 	$this->assertStringContainsString('"parentName": "button"', $generatedVariation);
// 	$this->assertFileExists($outputPath);
//  });

//  test('Variation CLI command will run under custom command name', function () {
// 	$variation = $this->variation;
// 	$result = $variation->getCommandName();

// 	expect($result)
// 		->toContain('variation');
// });

// test('Variation CLI documentation is correct', function () {
// 	expect($this->variation->getDoc())->toBeArray();
// });

// test('Variation CLI command will fail if Variation doesn\'t exist', function () {
// 	$variationMock = mock(BlockVariationCli::class)
// 		->makePartial()
// 		->shouldReceive('getFrontendLibsBlockPath')
// 		->andReturn(\dirname(__FILE__, 2) . '/data');

// 	$mock = $variationMock->getMock();

// 	$mock([], ['name' => 'testing']);
// })->expectException(InvalidBlock::class);
