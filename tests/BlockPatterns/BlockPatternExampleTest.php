<?php

namespace Tests\Unit\BlockPatterns;

use Brain\Monkey;
use Brain\Monkey\Functions;
use EightshiftBoilerplate\BlockPatterns\BlockPatternExample;

use function Tests\setupMocks;

beforeEach(function() {
	Monkey\setUp();
	setupMocks();

	$this->example = new BlockPatternExample();
});

afterEach(function() {
	Monkey\tearDown();
});

test('Register method will call init hook', function () {
	$this->example->register();

	$this->assertSame(10, has_action('init', 'EightshiftBoilerplate\BlockPatterns\BlockPatternExample->registerBlockPattern()'));
});

/**
 * This is a tricky one. Because it should be an integration test:
 * testing if the CPT was actually registered when the action runs.
 */
test('Register block pattern method will be called', function() {
	$action = 'block_pattern_registered';
	Functions\when('register_block_pattern')->justReturn(putenv("SIDEAFFECT={$action}"));

	$this->example->registerBlockPattern();

	$this->assertEquals(getenv('SIDEAFFECT'), $action);

	// Cleanup.
	putenv('SIDEAFFECT=');
});

// test('Is block pattern name defined and a string', function () {
// 	$this->assertNotEmpty($this->example->getName());
// 	$this->assertIsString(gettype($this->example->getName()));
// });

// test('Is block pattern title defined and a string', function () {
// 	$this->assertNotEmpty($this->example->getTitle());
// 	$this->assertIsString(gettype($this->example->getTitle()));
// });

// test('Is block pattern description defined and a string', function () {
// 	$this->assertNotEmpty($this->example->getDescription());
// 	$this->assertIsString(gettype($this->example->getDescription()));
// });

// test('Is block pattern content defined and a string', function () {
// 	$this->assertNotEmpty($this->example->getContent());
// 	$this->assertIsString(gettype($this->example->getContent()));
// });

// test('Is block pattern categories an array', function () {
// 	$this->assertIsArray(gettype($this->example->getCategories()));
// });

// test('Is block pattern keywords an array', function () {
// 	$this->assertIsArray(gettype($this->example->getKeywords()));
// });

// test('Is project version defined and a string', function () {
// 	$this->assertNotEmpty($this->example::getProjectVersion());
// 	$this->assertIsString(gettype($this->example::getProjectVersion()));
// });

// test('Is project REST namespace defined, a string and same as project name', function () {
// 	$this->assertNotEmpty($this->example::getProjectRoutesNamespace());
// 	$this->assertIsString(gettype($this->example::getProjectRoutesNamespace()));
// 	$this->assertEquals($this->example::getProjectName(), $this->example::getProjectRoutesNamespace());
// });

// test('Is project REST route version defined and a string', function () {
// 	$this->assertNotEmpty($this->example::getProjectRoutesVersion());
// 	$this->assertIsString(gettype($this->example::getProjectRoutesVersion()));
// 	$this->assertStringContainsString('v', $this->example::getProjectRoutesVersion());
// });

// test('Is project path defined and readable', function () {
// 	$this->assertNotEmpty($this->example::getProjectPath());
// 	$this->assertDirectoryIsReadable($this->example::getProjectPath());
// });

// test('Is custom project path defined and readable', function () {
// 	$this->assertNotEmpty($this->example::getProjectPath());
// 	$this->assertDirectoryIsReadable($this->example::getProjectPath('data/'));
// });

// test('If non-existent path throws exception', function () {
// 	$this->example::getProjectPath('bla/');
// })->throws(\EightshiftLibs\Exception\InvalidPath::class);
