<?php

namespace Tests\Unit\Block;

use EightshiftLibs\Blocks\BlockComponentCli;
use EightshiftLibs\Helpers\Components;
use Exception;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->mock = new BlockComponentCli('boilerplate');
});

afterEach(function () {
	setAfterEach();

	unset($this->mock);
});

 test('Component CLI command will correctly copy the Component class with defaults', function () {
	$mock = $this->mock;
	$mock([], [$this->mock->getDefaultArgs()]);

	$name = $this->mock->getDefaultArgs()['name'];

	$sep = \DIRECTORY_SEPARATOR;
	$output = \file_get_contents(Components::getProjectPaths('blocksDestinationComponents', "{$name}{$sep}{$name}.php"));

	expect($output)->toContain(
		'Fake component',
	);
});

test('Component CLI documentation is correct', function () {
	$mock = $this->mock;
	expect($mock->getDoc())->toBeArray();;
});

test('Component CLI command will fail if Component doesn\'t exist', function () {
	$mock = $this->mock;
	$mock([], array_merge(
		$mock->getDefaultArgs(),
		[
			'name' => 'testing'
		]
	));
})->throws(Exception::class, 'Requested item with the name "testing" doesn\'t exist in our library please review you search.\nYou can find all available items on this link: https://infinum.github.io/eightshift-docs/storybook/, \nor use this list for available items you can type:');
