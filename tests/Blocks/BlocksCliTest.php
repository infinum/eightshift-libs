<?php

namespace Tests\Unit\Blocks;

use EightshiftLibs\Blocks\BlocksCli;
use EightshiftLibs\Helpers\Components;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->mock = new BlocksCli('boilerplate');
});

afterEach(function () {
	setAfterEach();

	unset($this->mock);
});

test('Blocks CLI command will correctly copy the Blocks class with defaults', function () {
	$mock = $this->mock;
	$mock([], []);

	$output = \file_get_contents(Components::getProjectPaths('blocksDestination', "Blocks.php"));

	$this->assertStringContainsString('class Blocks extends AbstractBlocks', $output);
	$this->assertStringContainsString('@package EightshiftLibs\Blocks', $output);
	$this->assertStringContainsString('namespace EightshiftLibs\Blocks', $output);
	$this->assertStringNotContainsString('footer.php', $output);
});

test('Blocks CLI command will correctly copy the Blocks class with set arguments', function () {
	$mock = $this->mock;
	$mock([], [
		'namespace' => 'CoolTheme',
	]);

	$output = \file_get_contents(Components::getProjectPaths('blocksDestination', "Blocks.php"));

	$this->assertStringContainsString('namespace CoolTheme\Blocks;', $output);
});

test('Blocks CLI documentation is correct', function () {
	expect($this->mock->getDoc())->toBeArray();
});
