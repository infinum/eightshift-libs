<?php

namespace Tests\Unit\Init;

use EightshiftLibs\Helpers\Components;
use EightshiftLibs\Init\InitBlocksCli;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->mock = new InitBlocksCli('boilerplate');
});

afterEach(function () {
	setAfterEach();

	unset($this->mock);
});

test('Blocks CLI command will correctly copy the Blocks class with defaults', function () {
	$mock = $this->mock;
	$mock([], []);

	$output = \file_get_contents(Components::getProjectPaths('blocksDestination', "Blocks.php"));

	expect($output)
		->toBeString();
});
