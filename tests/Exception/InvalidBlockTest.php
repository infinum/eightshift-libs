<?php

namespace Tests\Unit\Exception;

use EightshiftLibs\Exception\InvalidBlock;

use function Tests\setupMocks;

beforeAll(function () {
	setupMocks();
});

test('Throws error if blocks are missing.', function () {

	$missingBlocks = InvalidBlock::missingBlocksException();

	$this->assertIsObject($missingBlocks);
	$this->assertObjectHasAttribute('message', $missingBlocks);
	$this->assertStringContainsString('There are no blocks added in your project.', $missingBlocks->getMessage());
});

test('Throws error if components are missing.', function () {

	$missingComponents = InvalidBlock::missingComponentsException();

	$this->assertIsObject($missingComponents);
	$this->assertObjectHasAttribute('message', $missingComponents);
	$this->assertStringContainsString('There are no components added in your project.', $missingComponents->getMessage());
});
