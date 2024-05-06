<?php

namespace Tests\Unit\Init;

use EightshiftLibs\Helpers\Helpers;
use EightshiftLibs\Init\InitBlocksCli;

use function Tests\getMockArgs;

beforeEach(function () {
	$this->mock = new InitBlocksCli('boilerplate');
});

afterEach(function () {
	unset($this->mock);
});

test('Blocks CLI command will correctly copy the Blocks class with defaults', function () {
	$mock = $this->mock;
	$mock([], getMockArgs());

	$output = \file_get_contents(Helpers::getProjectPaths('blocksDestination', "Blocks.php"));

	expect($output)
		->toBeString();
});
