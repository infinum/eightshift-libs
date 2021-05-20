<?php

namespace Tests\Unit\Blocks;

use Brain\Monkey;
use Brain\Monkey\Functions;
use EightshiftBoilerplate\Blocks\BlocksExample;
use EightshiftLibs\Exception\InvalidBlock;

use function Tests\mock;
use function Tests\setupMocks;

beforeEach(function() {
	Monkey\setUp();
	setupMocks();

	$this->config = mock('alias:EightshiftBoilerplate\Config\Config');

	$this->blocksExample = new BlocksExample();
});

afterEach(function() {
	Monkey\tearDown();
});

test('Register method will call init hooks', function () {
	$this->config
		->shouldReceive('getProjectPath')
		->andReturn('tests/data');

	$this->blocksExample->register();

	$this->assertSame(10, has_action('init', 'EightshiftBoilerplate\Blocks\BlocksExample->getBlocksDataFullRaw()'), 'The callback getBlocksDataFullRaw should be hooked to init hook with priority 10');
	$this->assertSame(11, has_action('init', 'EightshiftBoilerplate\Blocks\BlocksExample->registerBlocks()'), 'The callback registerBlocks should be hooked to init hook with priority 11');
});

test('Register method will call block_categories hooks', function () {
	$this->blocksExample->register();

	$this->assertSame(10, has_filter('block_categories', 'EightshiftBoilerplate\Blocks\BlocksExample->getCustomCategory()'), 'The callback getCustomCategory should be hooked to block_categories hook with priority 10');
});

test('Register method will call after_setup_theme hooks', function () {
	$this->blocksExample->register();

	$this->assertSame(25, has_action('after_setup_theme', 'EightshiftBoilerplate\Blocks\BlocksExample->addThemeSupport()'), 'The callback addThemeSupport should be hooked to after_setup_theme hook with priority 25');
	$this->assertSame(11, has_action('after_setup_theme', 'EightshiftBoilerplate\Blocks\BlocksExample->changeEditorColorPalette()'), 'The callback changeEditorColorPalette should be hooked to after_setup_theme hook with priority 10');
});

test('Register method will call admin_menu hooks', function () {
	$this->blocksExample->register();

	$this->assertSame(10, has_action('admin_menu', 'EightshiftBoilerplate\Blocks\BlocksExample->addReusableBlocks()'), 'The callback addReusableBlocks should be hooked to admin_menu hook with priority 10');
});

test('Register method will call custom hooks', function () {
	$this->blocksExample->register();

	$this->assertSame(10, has_filter(BlocksExample::BLOCKS_DEPENDENCY_FILTER_NAME, 'EightshiftBoilerplate\Blocks\BlocksExample->getBlocksDataFullRawItem()'));
});

test('addThemeSupport method will call add_theme_support() function with different arguments', function () {

	Functions\when('add_theme_support')->alias(function($arg) {
		$envName = strtoupper($arg);
		$envName = \str_replace('-', '_', $envName);
		putenv("{$envName}=true");
	});

	$this->blocksExample->addThemeSupport();

	$this->assertSame(getenv('ALIGN_WIDE'), 'true', "Method addThemeSupport() didn't add theme support for align-wide");

});

test('Asserts that getAllBlocksList first argument is boolean and return the provided attribute as return value.', function () {

	$post = \Mockery::mock('WP_Post');

	$blocks = $this->blocksExample->getAllBlocksList(true, $post);

	$this->assertEquals(true, $blocks);

	$blocks = $this->blocksExample->getAllBlocksList(false, $post);

	$this->assertEquals(false, $blocks, "Return value is not false.");
});

test('Asserts that getAllBlocksList will return only projects blocks.', function () {

	$post = \Mockery::mock('WP_Post');

	$this->config
		->shouldReceive('getProjectPath')
		->andReturn('tests/data');

	$this->blocksExample->getBlocksDataFullRaw();

	$list = $this->blocksExample->getAllBlocksList([], $post);

	$this->assertIsArray($list);
	$this->assertNotContains('core/paragraph', $list, "List array does contain core/paragraph item.");
	$this->assertContains('eightshift-boilerplate/button', $list, "List array doesn't contain eightshift-boilerplate/button item.");
	$this->assertContains('core/block', $list, "List array doesn't contain core/block item.");
	$this->assertContains('core/template', $list, "List array doesn't contain core/template item.");
});

test('Asserts that getBlocksDataFullRawItem will return full details for blocks if key is not provided.', function () {
	$this->config
		->shouldReceive('getProjectPath')
		->andReturn('tests/data');

	$this->blocksExample->getBlocksDataFullRaw();

	$items = $this->blocksExample->getBlocksDataFullRawItem('');

	$this->assertIsArray($items);
	$this->assertNotContains('button', array_keys($items), "Items array contains button key");
	$this->assertContains('dependency', array_keys($items), "Items array doesn't contain dependency key");
	$this->assertContains('blocks', array_keys($items), "Items array doesn't contain blocks key");
	$this->assertContains('components', array_keys($items), "Items array doesn't contain components key");
	$this->assertContains('wrapper', array_keys($items), "Items array doesn't contain wrapper key");
	$this->assertContains('settings', array_keys($items), "Items array doesn't contain settings key");
});

test('Asserts that getBlocksDataFullRawItem will return all blocks details if key is default.', function () {
	$this->config
		->shouldReceive('getProjectPath')
		->andReturn('tests/data');

	$this->blocksExample->getBlocksDataFullRaw();

	$items = $this->blocksExample->getBlocksDataFullRawItem();

	$this->assertIsArray($items);
	$this->assertContains('blockName', array_keys($items[0]), "Items array doesn't contain blockName key");
	$this->assertEquals('button', $items[0]['blockName'], "Items array doesn't contain blockName key with value button");
	$this->assertNotContains('componentName', array_keys($items[0]), "Items array does contain componentName key");
	$this->assertNotEquals('test', $items[0]['blockName'], "Items array contain blockName key with value test");
});

test('Asserts that getBlocksDataFullRawItem will return all components details if key is components.', function () {
	$this->config
		->shouldReceive('getProjectPath')
		->andReturn('tests/data');

	$this->blocksExample->getBlocksDataFullRaw();

	$items = $this->blocksExample->getBlocksDataFullRawItem('components');

	$this->assertIsArray($items);
	$this->assertContains('componentName', array_keys($items[0]), "Items array doesn't contain componentName key");
	$this->assertEquals('button', $items[0]['componentName'], "Items array doesn't contain componentName key with value button");
	$this->assertNotContains('blockName', array_keys($items[0]), "Items array does contain blockName key");
	$this->assertNotEquals('test', $items[0]['componentName'], "Items array contain componentName key with value test");
});

test('Asserts that getBlocksDataFullRawItem will return empty array if code is run using WP_CLI.', function () {

	if (!defined('WP_CLI')) {
		define('WP_CLI', true);
	}

	putenv('TEST');

	$items = $this->blocksExample->getBlocksDataFullRawItem();

	$this->assertIsArray($items);
	$this->assertEmpty($items);
	$this->assertNotContains('componentName', $items, "Items array contains componentName");
	putenv('TEST=1');
});

test('Asserts that render component will load view template.', function () {

	$blockManifest = [
		'blockName' => 'button',
	];

	$this->config
		->shouldReceive('getProjectPath')
		->andReturn('tests/data');

	$block = $this->blocksExample->render($blockManifest, '');

	$this->assertStringContainsString('Wrapper!', $block);
	$this->assertStringNotContainsString('fake', $block, "Blocks render contains fake string.");
});

test('Asserts that render will throw error if block view is missing.', function () {
	$blockManifest = [
		'blockName' => 'fake',
	];

	$this->config
		->shouldReceive('getProjectPath')
		->andReturn('tests/data');

	$this->blocksExample->render($blockManifest, '');
})->throws(InvalidBlock::class);

test('Asserts that render will throw error if wrapper view is missing.', function () {
	$blockManifest = [
		'blockName' => 'fake',
	];

	$this->config
		->shouldReceive('getProjectPath')
		->andReturn('fake');

	$this->blocksExample->render($blockManifest, '');

})->throws(InvalidBlock::class);

test('Asserts that renderWrapperView will return a valid file.', function () {
	$wrapperManifest = dirname(__FILE__, 2) . '/data/src/Blocks/wrapper/wrapper.php';

	ob_start();
	$this->blocksExample->renderWrapperView($wrapperManifest, []);
	$content = ob_get_clean();

	$this->assertSame('<div>Wrapper!</div>', trim($content));
});

test('Asserts that renderWrapperView will throw error if path is not valid.', function () {
	$this->blocksExample->renderWrapperView('fake path', []);
})->throws(InvalidBlock::class);

test('Asserts that getCustomCategory will return categories array.', function () {

	$post = \Mockery::mock('WP_Post');
	$category = $this->blocksExample->getCustomCategory([], $post);

	$this->assertIsArray($category);
	$this->assertContains('eightshift', $category[0], "Items array doesn't contain eightshift category");
});

test('Asserts that getCustomCategory will throw error if first argument is not array.', function () {

	$post = \Mockery::mock('WP_Post');
	$this->blocksExample->getCustomCategory('', $post);

})->throws(\TypeError::class);

test('changeEditorColorPalette method will call add_theme_support() function with if colors exist.', function () {

	Functions\when('add_theme_support')->alias(function($arg) {
		$envName = strtoupper($arg);
		$envName = \str_replace('-', '_', $envName);
		putenv("{$envName}=true");
	});

	$this->config
		->shouldReceive('getProjectPath')
		->andReturn('tests/data');

	$this->blocksExample->getBlocksDataFullRaw();

	$this->blocksExample->changeEditorColorPalette();

	$this->assertSame(getenv('EDITOR_COLOR_PALETTE'), 'true', "Method addThemeSupport() didn't add theme support for editor-color-palette");
});

test('registerBlocks method will register all blocks.', function () {

	putenv('BLOCK_TYPE=false');

	Functions\when('register_block_type')->alias(function(string $name, array $args = []) {
		putenv('BLOCK_TYPE=true');
	});

	$this->config
		->shouldReceive('getProjectPath')
		->andReturn('tests/data');

	$this->blocksExample->getBlocksDataFullRaw();

	$this->blocksExample->registerBlocks();

	$this->assertSame(getenv('BLOCK_TYPE'), 'true', 'Calling void method register_block_type caused no sideaffects');
});

test('registerBlocks method will throws error if blocks are not registered.', function () {
	$this->blocksExample->registerBlocks();
})->throws(InvalidBlock::class);
