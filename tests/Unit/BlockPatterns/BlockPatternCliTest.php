<?php

namespace Tests\Unit\BlockPatterns;

use EightshiftLibs\BlockPatterns\BlockPatternCli;
use EightshiftLibs\Helpers\Components;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->mock = new BlockPatternCli('boilerplate');
});

afterEach(function () {
	setAfterEach();

	unset($this->mock);
});

//---------------------------------------------------------------------------------//

test('Block pattern CLI command will correctly copy the Block Pattern class with defaults', function () {
	$mock = $this->mock;
	$mock([], $this->mock->getDefaultArgs());

	// Check the output dir if the generated method is correctly generated.
	$sep = \DIRECTORY_SEPARATOR;

	$output = \file_get_contents(Components::getProjectPaths('srcDestination', "BlockPatterns{$sep}ExampleTitleBlockPattern.php"));

	expect($output)
		->toContain(
			'class ExampleTitleBlockPattern',
			'example-title',
			'example-name',
			'example-description',
			'example-content'
		)
		->not->toContain(
			'class BlockPatternExample',
			'%title%',
			'%name%',
			'%description%',
			'%content%'
		);
});


test('Block pattern CLI command will correctly copy the Block pattern class with set arguments', function () {
	$mock = $this->mock;
	$mock([], [
		'title' => 'Your Own Thing',
		'name' => 'eightshift-boilerplate/your-own-thing',
		'description' => 'Description of the your own thing pattern',
		'content' => 'this-one-has-some-content',
	]);

	// Check the output dir if the generated method is correctly generated.
	$output = \file_get_contents(\dirname(__FILE__, 3) . '/cliOutput/src/BlockPatterns/YourOwnThingBlockPattern.php');

	expect($output)
	->toContain(
		'class YourOwnThingBlockPattern',
		'Your Own Thing',
		'eightshift-boilerplate/your-own-thing',
		'Description of the your own thing pattern',
		'this-one-has-some-content'
	)
	->not->toContain(
		'class BlockPatternExample',
		'%title%',
		'%name%',
		'%description%',
		'%content%'
	);
});

test('Block pattern CLI command will generate a name from title if "name" argument is not provided', function () {
	$mock = $this->mock;
	$mock([], [
		'title' => 'Your Own Thing',
		'name' => '',
		'description' => 'Description of the your own thing pattern',
		'content' => 'this-one-has-some-content',
	]);

	$output = \file_get_contents(\dirname(__FILE__, 3) . '/cliOutput/src/BlockPatterns/YourOwnThingBlockPattern.php');

	expect($output)
	->toContain(
		'class YourOwnThingBlockPattern',
		'Your Own Thing',
		'eightshift-boilerplate/your-own-thing',
		'Description of the your own thing pattern',
		'this-one-has-some-content'
	)
	->not->toContain(
		'class BlockPatternExample',
		'%title%',
		'%name%',
		'%description%',
		'%content%'
	);
});

test('getDoc will return correct array', function () {
	$docs = $this->mock->getDoc();

	expect($docs)
		->toBeArray()
		->toHaveKeys(['shortdesc', 'synopsis', 'longdesc'])
		->and(count($docs['synopsis']))->toEqual(4)
		->and($docs['synopsis'][0]['name'])->toEqual('title')
		->and($docs['synopsis'][1]['name'])->toEqual('name')
		->and($docs['synopsis'][2]['name'])->toEqual('description')
		->and($docs['synopsis'][3]['name'])->toEqual('content');
});
