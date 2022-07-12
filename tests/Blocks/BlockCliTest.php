<?php

namespace Tests\Unit\Block;

use EightshiftLibs\Blocks\UseBlockCli;

use EightshiftLibs\Helpers\Components;
use Exception;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->mock = new UseBlockCli('boilerplate');
});

afterEach(function () {
	setAfterEach();

	unset($this->mock);
});

test('Block CLI command will correctly copy the Block class with defaults', function () {
	$mock = $this->mock;
	$mock([], $mock->getDefaultArgs());

	$name = $this->mock->getDefaultArgs()['name'];

	$sep = \DIRECTORY_SEPARATOR;
	$output = \file_get_contents(Components::getProjectPaths('blocksDestinationCustom', "{$name}{$sep}{$name}.php"));

	expect($output)->toContain(
		'Template for the Button Block view.',
		'@package EightshiftLibs',
	)
	->not->toContain(
		'@package EightshiftBoilerplate'
	);
});

test('Block CLI documentation is correct', function () {
	$mock = $this->mock;
	expect($mock->getDoc())->toBeArray();
});

test('Block CLI command will fail if block doesn\'t exist', function () {
	$mock = $this->mock;
	$mock([], array_merge(
		$mock->getDefaultArgs(),
		[
			'name' => 'testing'
		]
	));
})->throws(Exception::class, 'You can find all available items on this list:');
