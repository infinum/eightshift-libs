<?php

namespace Tests\Unit\Block;

use EightshiftLibs\Blocks\UseWrapperCli;
use EightshiftLibs\Helpers\Components;

beforeEach(function () {
	$this->mock = new UseWrapperCli('boilerplate');
});

afterEach(function () {
	unset($this->mock);
});

test('Wrapper CLI command will correctly copy the Wrapper class with defaults', function () {
	$mock = $this->mock;
	$mock([], []);

	$output = \file_get_contents(Components::getProjectPaths('blocksDestinationWrapper', 'wrapper.php'));

	expect($output)->toContain('Fake wrapper');
});

test('Wrapper CLI command will run under custom command name', function () {
	$mock = $this->mock;
	$result = $mock->getCommandName();

	expect($result)->toContain('wrapper');
});

test('Wrapper CLI documentation is correct', function () {
	expect($this->mock->getDoc())->toBeArray();
});
