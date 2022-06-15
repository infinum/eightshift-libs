<?php

namespace Tests\Unit\Columns\Media;

use EightshiftLibs\Columns\Media\WebPMediaColumnCli;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->webPMediaColumnCliMock = new WebPMediaColumnCli('boilerplate');
});

afterEach(function () {
	setAfterEach();
});

test('Check if CLI command name is correct.', function () {
	$mock = $this->webPMediaColumnCliMock;

	$mock = $mock->getCommandName();

	expect($mock)
		->toBeString()
		->toEqual('webp_media_column');
});

test('Check if CLI command documentation is correct.', function () {
	expect($this->webPMediaColumnCliMock->getDoc())->toBeArray();
});

test('Check if CLI command will correctly copy the WebPMediaColums class with defaults.', function () {
	$mock = $this->webPMediaColumnCliMock;

	$mock([], $mock->getDefaultArgs());

	// Check the output dir if the generated method is correctly generated.
	$output = \file_get_contents(\dirname(__FILE__, 4) . '/cliOutput/src/Columns/Media/WebPMediaColumn.php');

	expect($output)
		->not->toBeEmpty()
		->toContain('WebPMediaColumn extends AbstractMediaColumns')
		->toContain("COLUMN_KEY = 'webp'");
});
