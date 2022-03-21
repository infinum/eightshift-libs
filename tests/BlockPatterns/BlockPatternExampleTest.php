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

	$this->assertSame(\getenv('SIDEAFFECT'), $action);

	// Cleanup.
	putenv('SIDEAFFECT=');
});
