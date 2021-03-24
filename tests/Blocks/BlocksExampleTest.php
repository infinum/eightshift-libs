<?php

namespace Tests\Unit\Blocks;

use Brain\Monkey;
use EightshiftBoilerplate\Blocks\BlocksExample;

use function Tests\setupMocks;
use function Tests\mock;

beforeEach(function() {
	Monkey\setUp();
	setupMocks();

	$this->blocks = new BlocksExample();
});

afterEach(function() {
	Monkey\tearDown();
});

test('Register method will call init hooks', function () {
	$this->blocks->register();

	$this->assertSame(10, has_action('init', 'EightshiftBoilerplate\Blocks\BlocksExample->getBlocksDataFullRaw()'));
	$this->assertSame(11, has_action('init', 'EightshiftBoilerplate\Blocks\BlocksExample->registerBlocks()'));
});

test('Register method will call block_categories hooks', function () {
	$this->blocks->register();

	$this->assertSame(10, has_filter('block_categories', 'EightshiftBoilerplate\Blocks\BlocksExample->getCustomCategory()'));
});

test('Register method will call after_setup_theme hooks', function () {
	$this->blocks->register();

	$this->assertSame(25, has_action('after_setup_theme', 'EightshiftBoilerplate\Blocks\BlocksExample->addThemeSupport()'));
	$this->assertSame(11, has_action('after_setup_theme', 'EightshiftBoilerplate\Blocks\BlocksExample->changeEditorColorPalette()'));
});

test('Register method will call admin_menu hooks', function () {
	$this->blocks->register();

	$this->assertSame(10, has_action('admin_menu', 'EightshiftBoilerplate\Blocks\BlocksExample->addReusableBlocks()'));
});

test('Register method will call custom hooks', function () {
	$this->blocks->register();

	$this->assertSame(10, has_filter(BlocksExample::BLOCKS_DEPENDENCY_FILTER_NAME, 'EightshiftBoilerplate\Blocks\BlocksExample->getBlocksDataFullRawItem()'));
});
