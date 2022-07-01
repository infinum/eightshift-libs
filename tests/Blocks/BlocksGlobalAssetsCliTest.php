<?php

namespace Tests\Unit\Block;

use EightshiftLibs\Blocks\BlocksGlobalAssetsCli;
use EightshiftLibs\Helpers\Components;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->mock = new BlocksGlobalAssetsCli('boilerplate');
});

afterEach(function () {
	setAfterEach();

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
