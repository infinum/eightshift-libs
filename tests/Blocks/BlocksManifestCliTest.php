<?php

namespace Tests\Unit\Block;

use EightshiftLibs\Blocks\BlocksManifestCli;
use EightshiftLibs\Helpers\Components;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->mock = new BlocksManifestCli('boilerplate');
});

afterEach(function () {
	setAfterEach();

	unset($this->mock);
});

test('Manifest CLI command will correctly copy the manifest.json file with defaults', function () {
	$mock = $this->mock;
	$mock([], []);

	$output = \file_get_contents(Components::getProjectPaths('blocksSource', 'manifest.json'));

	expect($output)->toContain('namespace');
});

test('Manifest CLI documentation is correct', function () {
	expect($this->mock->getDoc())->toBeArray();
});
