<?php

namespace Tests\Unit\Columns\Media;

use EightshiftLibs\Columns\Media\WebPMediaColumnCli;
use EightshiftLibs\Helpers\Components;

beforeEach(function () {
	$this->webPMediaColumnCliMock = new WebPMediaColumnCli('boilerplate');
});

afterEach(function () {
	unset($this->webPMediaColumnCliMock);
});

test('Check if CLI command name is correct.', function () {
	$mock = $this->webPMediaColumnCliMock;

	$mock = $mock->getCommandName();

	expect($mock)
		->toBeString()
		->toEqual('webp-media-column');
});

test('Check if CLI command documentation is correct.', function () {
	expect($this->webPMediaColumnCliMock->getDoc())->toBeArray();
});

test('Check if CLI command will correctly copy the WebPMediaColumns class with defaults.', function () {
	$mock = $this->webPMediaColumnCliMock;

	$mock([], $mock->getDefaultArgs());

	// Check the output dir if the generated method is correctly generated.
	$output = \file_get_contents(Components::getProjectPaths('srcDestination', 'Columns/Media/WebPMediaColumn.php'));

	expect($output)
		->not->toBeEmpty()
		->toContain('WebPMediaColumn extends AbstractMediaColumns')
		->toContain("COLUMN_KEY = 'webp'");
});
