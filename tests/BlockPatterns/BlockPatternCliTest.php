<?php

namespace Tests\Unit\BlockPatterns;

use EightshiftLibs\BlockPatterns\BlockPatternCli;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;
use function Tests\getCliOutputPath;

beforeEach(function () {
	setBeforeEach();

	$this->blockPattern = new BlockPatternCli('boilerplate');
});

afterEach(function () {
	setAfterEach();

	unset($this->blockPattern);
});

//---------------------------------------------------------------------------------//

test('Block pattern CLI command will correctly copy the Block Pattern class with defaults', function () {
	$blockPattern = $this->blockPattern;
	$blockPattern([], $this->blockPattern->getDefaultArgs([]));

	// Check the output dir if the generated method is correctly generated.
	$output = \file_get_contents(getCliOutputPath('src/BlockPatterns/ExampleTitleBlockPattern.php'));

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
	$blockPattern = $this->blockPattern;
	$blockPattern([], [
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
	$blockPattern = $this->blockPattern;
	$blockPattern([], [
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


// test('Block Pattern documentation is correct', function () {
// 	expect($this->blockPattern->getDoc())->toBeArray();
// });
