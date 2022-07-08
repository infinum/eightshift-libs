<?php

namespace Tests\Unit\Block;

use EightshiftLibs\Blocks\UseAssetsCli;
use EightshiftLibs\Helpers\Components;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->mock = new UseAssetsCli('boilerplate');
});

afterEach(function () {
	setAfterEach();

	unset($this->mock);
});

test('Assets CLI command will correctly copy the Assets class with defaults', function () {
	$mock = $this->mock;
	$mock([], []);

	$output = \file_get_contents(Components::getProjectPaths('blocksAssetsDestination', 'assets.php'));

	expect($output)->toContain('Fake assets');
});

test('Assets CLI documentation is correct', function () {
	expect($this->mock->getDoc())->toBeArray();
});
