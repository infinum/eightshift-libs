<?php

namespace Tests\Unit\Block;

use EightshiftLibs\Blocks\BlockComponentCli;

use EightshiftLibs\Exception\InvalidBlock;

use function Tests\mock;
use function Tests\setAfterEach;
use function Tests\setBeforeEach;

/**
 * Mock before tests.
 */
beforeEach(function () {
	setBeforeEach();

	$this->component = new BlockComponentCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	setAfterEach();

	unset($this->component);
});

 test('Component CLI command will correctly copy the Component class with defaults', function () {
	$componentMock = mock(BlockComponentCli::class)
		->makePartial()
		->shouldReceive('getFrontendLibsBlockPath')
		->andReturn(\dirname(__FILE__, 2) . '/data');

	$mock = $componentMock->getMock();

	$mock([], [$this->component->getDefaultArgs()]);

	$outputPath = \dirname(__FILE__, 3) . '/cliOutput/button/button.php';

	// Check the output dir if the generated method is correctly generated.
	$generatedComponent = \file_get_contents($outputPath);

	$this->assertStringContainsString('<div>Hello!</div>', $generatedComponent);
	$this->assertFileExists($outputPath);
 });

 test('Component CLI command will run under custom command name', function () {
	$component = $this->component;
	$result = $component->getCommandName();

	$this->assertStringContainsString('component', $result);
});

test('Component CLI documentation is correct', function () {
	expect($this->component->getDoc())->toBeArray();
});

test('Component CLI command will fail if Component doesn\'t exist', function () {
	$componentMock = mock(BlockComponentCli::class)
		->makePartial()
		->shouldReceive('getFrontendLibsBlockPath')
		->andReturn(\dirname(__FILE__, 2) . '/data');

	$mock = $componentMock->getMock();

	$mock([], ['name' => 'testing']);
})->expectException(InvalidBlock::class);
