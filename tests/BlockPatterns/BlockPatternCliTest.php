<?php

namespace Tests\Unit\BlockPatterns;

use EightshiftLibs\BlockPatterns\BlockPatternCli;

use function Tests\deleteCliOutput;
use function Tests\mock;

/**
 * Mock before tests.
 */
beforeEach(function () {
	$wpCliMock = mock('alias:WP_CLI');

	$wpCliMock
		->shouldReceive('success')
		->andReturnArg(0);

	$wpCliMock
		->shouldReceive('error')
		->andReturnArg(0);

	$this->blockPattern = new BlockPatternCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	$output = dirname(__FILE__, 3) . '/cliOutput';

	deleteCliOutput($output);
});


test('Block pattern CLI command will correctly copy the Block Pattern class with defaults', function () {
	$blockPattern = $this->blockPattern;
	$developArgs = $blockPattern->getDevelopArgs([]);
	$blockPattern([], $developArgs);

	// Check the output dir if the generated method is correctly generated.
	$generatedBlockPattern = file_get_contents(dirname(__FILE__, 3) . "/cliOutput/src/BlockPatterns/SomethingBlockPattern.php");

	$this->assertStringContainsString('class SomethingBlockPattern extends AbstractBlockPattern', $generatedBlockPattern);

	foreach ($developArgs as $developArg) {
		$this->assertStringContainsString($developArg, $generatedBlockPattern);
	}

	$this->assertStringNotContainsString('example-content', $generatedBlockPattern);
	$this->assertStringNotContainsString('example-description', $generatedBlockPattern);
	$this->assertStringNotContainsString('example-title', $generatedBlockPattern);
	$this->assertStringNotContainsString('example-name', $generatedBlockPattern);
});


test('Block pattern CLI command will correctly copy the Block pattern class with set arguments', function () {
	$blockPattern = $this->blockPattern;
	$cliArgs = [
		'title' => 'Your Own Thing',
		'name' => 'eightshift-boilerplate/your-own-thing',
		'description' => 'Description of the your own thing pattern',
		'content' => 'this-one-has-some-content',
	];
	$blockPattern([], $cliArgs);

	// Check the output dir if the generated method is correctly generated.
	$generatedBlockPattern = file_get_contents(dirname(__FILE__, 3) . '/cliOutput/src/BlockPatterns/YourOwnThingBlockPattern.php');

	$this->assertStringContainsString('class YourOwnThingBlockPattern extends AbstractBlockPattern', $generatedBlockPattern);
	foreach ($cliArgs as $cliArg) {
		$this->assertStringContainsString($cliArg, $generatedBlockPattern);
	}

	$this->assertStringNotContainsString('example-content', $generatedBlockPattern);
	$this->assertStringNotContainsString('example-description', $generatedBlockPattern);
	$this->assertStringNotContainsString('example-title', $generatedBlockPattern);
	$this->assertStringNotContainsString('example-name', $generatedBlockPattern);
});

test('Block pattern CLI command will generate a name from title if "name" argument is not provided', function () {
	$blockPattern = $this->blockPattern;
	$cliArgs = [
		'title' => 'Your Own Thing',
		'description' => 'Description of the your own thing pattern',
		'content' => 'this-one-has-some-content',
	];
	$blockPattern([], $cliArgs);

	// Check the output dir if the generated method is correctly generated.
	$generatedBlockPattern = file_get_contents(dirname(__FILE__, 3) . '/cliOutput/src/BlockPatterns/YourOwnThingBlockPattern.php');

	$this->assertStringContainsString('eightshift-boilerplate/your-own-thing', $generatedBlockPattern);
});


test('Block Pattern documentation is correct', function () {
	$blockPattern = $this->blockPattern;

	$documentation = $blockPattern->getDoc();

	$key = 'shortdesc';

	$this->assertIsArray($documentation);
	$this->assertArrayHasKey($key, $documentation);
	$this->assertArrayHasKey('synopsis', $documentation);
	$this->assertSame('Generates a block pattern.', $documentation[$key]);
});
