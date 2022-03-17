<?php

namespace Tests\Unit\Block;

use EightshiftLibs\Blocks\BlockComponentCli;

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

	$this->component = new BlockComponentCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	$output = \dirname(__FILE__, 3) . '/cliOutput';

	deleteCliOutput($output);
});

 test('Component CLI command will correctly copy the Component class with defaults', function () {
	$componentMock = mock(BlockComponentCli::class)
		->makePartial()
		->shouldReceive('getFrontendLibsBlockPath')
		->andReturn(\dirname(__FILE__, 2) . '/data');

	$mock = $componentMock->getMock();

	$mock([], [$this->component->getDevelopArgs([])]);

	$outputPath = \dirname(__FILE__, 3) . '/cliOutput/button/button.php';

	// Check the output dir if the generated method is correctly generated.
	$generatedComponent = \file_get_contents($outputPath);

	$this->assertStringContainsString('<div>Hello!</div>', $generatedComponent);
	$this->assertFileExists($outputPath);
 });

 test('Component CLI command will run under custom command name', function () {
	$component = $this->component;
	$result = $component->getCommandName();

	$this->assertStringContainsString('use_component', $result);
});

test('Component CLI documentation is correct', function () {
	$component = $this->component;

	$documentation = $component->getDoc();

	$key = 'shortdesc';

	$this->assertIsArray($documentation);
	$this->assertArrayHasKey($key, $documentation);
	$this->assertArrayHasKey('synopsis', $documentation);
	$this->assertSame('Copy Component from library to your project.', $documentation[$key]);
});

test('Component CLI command will fail if Component doesn\'t exist', function () {
	$componentMock = mock(BlockComponentCli::class)
		->makePartial()
		->shouldReceive('getFrontendLibsBlockPath')
		->andReturn(\dirname(__FILE__, 2) . '/data');

	$mock = $componentMock->getMock();

	$mock([], ['name' => 'testing']);
})->expectException(InvalidBlock::class);
