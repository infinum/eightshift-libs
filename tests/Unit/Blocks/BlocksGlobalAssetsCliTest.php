<?php

namespace Tests\Unit\Block;

use EightshiftLibs\Blocks\UseGlobalAssetsCli;
use EightshiftLibs\Helpers\Components;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->mock = new UseGlobalAssetsCli('boilerplate');
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
