<?php

namespace Tests\Unit\Block;

use EightshiftLibs\Blocks\UseStorybookCli;
use EightshiftLibs\Helpers\Components;

beforeEach(function () {
	$this->mock = new UseStorybookCli('boilerplate');
});

afterEach(function () {
	unset($this->mock);
});

test('Storybook CLI command will correctly copy the Storybook class with defaults', function () {
	$mock = $this->mock;
	$mock([], []);

	$output = \file_get_contents(Components::getProjectPaths('blocksStorybookDestination', 'storybook.php'));

	expect($output)->toContain('Storybook example file');
});

test('Storybook CLI documentation is correct', function () {
	expect($this->mock->getDoc())->toBeArray();
});
