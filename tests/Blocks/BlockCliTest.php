<?php

namespace Tests\Unit\Block;

use EightshiftLibs\Blocks\BlockCli;

use EightshiftLibs\Exception\InvalidBlock;
use EightshiftLibs\Helpers\Components;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->mock = new BlockCli('boilerplate');
});

afterEach(function () {
	setAfterEach();

	unset($this->mock);
});

test('Block CLI command will correctly copy the Block class with defaults', function () {
	$mock = $this->mock;
	$mock([], []);

	$sep = \DIRECTORY_SEPARATOR;
	$output = \file_get_contents(Components::getProjectPaths('cliOuput', "src{$sep}Blocks{$sep}custom{$sep}button{$sep}button.php"));

	$this->assertStringContainsString('Template for the Button Block view.', $output);
	$this->assertStringContainsString('@package EightshiftBoilerplate', $output);
	$this->assertStringNotContainsString('Components::render(\'link\', $attributes)', $output);
 });

test('Block CLI command will run under custom command name', function () {
	$mock = $this->mock;
	$output = $mock->getCommandName();

	expect($output)->toContain('block');
});

test('Block CLI documentation is correct', function () {
	expect($this->mock->getDoc())->toBeArray();
});

test('Block CLI command will fail if block doesn\'t exist', function () {
	$mock = $this->mock;
	$mock([], ['name' => 'testing']);
})->expectException(InvalidBlock::class);
