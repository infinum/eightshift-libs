<?php

namespace Tests;

use Mockery;
use EightshiftBoilerplate\Blocks\BlocksExample;
use EightshiftLibs\Init\InitBlocksCli;
use Mockery\MockInterface;

/**
 * Build all blocks setup output.
 *
 * @return void
 */
function buildTestBlocks()
{
	(new InitBlocksCli('boilerplate'))->__invoke([], []);

	(new BlocksExample())->getBlocksDataFullRaw();
}

/**
 * Mock Mockery interface.
 *
 * @param string $class Class to mock.
 *
 * @return MockInterface
 */
function mock(string $classname): MockInterface
{
	return Mockery::mock($classname);
}
