<?php

namespace Tests\Unit\Block;

use EightshiftLibs\Blocks\UseGlobalAssetsCli;
use EightshiftLibs\Helpers\Components;

beforeEach(function () {
	$this->mock = new UseGlobalAssetsCli('boilerplate');
});

afterEach(function () {
	unset($this->mock);
});

test('Assets CLI command will correctly copy the Global Assets class with defaults', function () {
	$mock = $this->mock;
	$mock([], []);

	$output = \file_get_contents(Components::getProjectPaths('blocksGlobalAssetsDestination', 'assets.php'));

	expect($output)->toContain('Global Assets example file.');
});

test('Assets CLI documentation is correct', function () {
	expect($this->mock->getDoc())->toBeArray();
});
