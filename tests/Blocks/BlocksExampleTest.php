<?php

namespace Tests\Unit\Blocks;

use Brain\Monkey;
use Brain\Monkey\Functions;
use EightshiftBoilerplate\Blocks\BlocksExample;
use EightshiftLibs\Exception\InvalidBlock;

use WP_Block_Editor_Context;

use function Tests\mock;
use function Tests\setupMocks;

beforeEach(function() {
	Monkey\setUp();
	setupMocks();

	$this->config = mock('alias:EightshiftBoilerplate\Config\Config');

	Functions\when('is_wp_version_compatible')->justReturn(true);

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

test('Register method will call block_categories_all hooks', function () {

	$this->blocksExample->register();

	$this->assertSame(10, has_filter('block_categories_all', 'EightshiftBoilerplate\Blocks\BlocksExample->getCustomCategory()'), 'The callback getCustomCategory should be hooked to block_categories_all hook with priority 10');
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

test('addThemeSupport method will call add_theme_support() function with different arguments', function () {

	Functions\when('add_theme_support')->alias(function($arg) {
		$envName = strtoupper($arg);
		$envName = \str_replace('-', '_', $envName);
		putenv("{$envName}=true");
	});

	$this->blocksExample->addThemeSupport();

	$this->assertSame(\getenv('ALIGN_WIDE'), 'true', "Method addThemeSupport() didn't add theme support for align-wide");

});

test('Asserts that getAllBlocksList first argument is boolean and return the provided attribute as return value for older WP versions.', function () {

	Functions\when('is_wp_version_compatible')->justReturn(false);

	$post = mock('WP_Post');

	$blocks = $this->blocksExample->getAllBlocksListOld(true, $post);

	$this->assertSame(true, $blocks);

	$blocks = $this->blocksExample->getAllBlocksListOld(false, $post);

	$this->assertSame(false, $blocks, "Return value is not false.");
});

test('Asserts that getAllBlocksList will return true if post type is eightshift-forms for WP 5.8.', function () {

	$blockContext = mock('WP_Block_Editor_Context');
	$blockContext->post = mock('WP_Post');
	$blockContext->post->post_type = 'eightshift-forms';

	$blocks = $this->blocksExample->getAllBlocksList(true, $blockContext);

	$this->assertSame(true, $blocks);

	$blockContext->post = mock('WP_Post');
	$blockContext->post->post_type = 'post';

	$blocks = $this->blocksExample->getAllBlocksList(false, $blockContext);

	$this->assertSame(false, $blocks);
});

test('Asserts that getAllBlocksList first argument is boolean and return the provided attribute as return value for WP 5.8.', function () {

	$blockContext = mock('WP_Block_Editor_Context');
	$blockContext->post = mock('WP_Post');
	$blockContext->post->post_type = 'post';

	$blocks = $this->blocksExample->getAllBlocksList(true, $blockContext);

	$this->assertSame(true, $blocks);

	$blocks = $this->blocksExample->getAllBlocksList(false, $blockContext);

	$this->assertSame(false, $blocks, "Return value is not false.");
});

test('Asserts that getAllBlocksList will return only projects blocks for older versions.', function () {

	Functions\when('is_wp_version_compatible')->justReturn(false);

	$post = mock(\WP_Post::class);

	$this->config
		->shouldReceive('getProjectPath')
		->andReturn('tests/data');

	$this->blocksExample->getBlocksDataFullRaw();

	$list = $this->blocksExample->getAllBlocksListOld([], $post);

	$this->assertIsArray($list);
	$this->assertNotContains('core/paragraph', $list, "List array does contain core/paragraph item.");
	$this->assertContains('eightshift-boilerplate/button', $list, "List array doesn't contain eightshift-boilerplate/button item.");
	$this->assertContains('core/block', $list, "List array doesn't contain core/block item.");
	$this->assertContains('core/template', $list, "List array doesn't contain core/template item.");
});

test('Asserts that getAllBlocksList will return only projects blocks for WP 5.8.', function () {

	$blockContext = mock(WP_Block_Editor_Context::class);
	$blockContext->post = null;

	$this->config
		->shouldReceive('getProjectPath')
		->andReturn('tests/data');

	$this->blocksExample->getBlocksDataFullRaw();

	$list = $this->blocksExample->getAllBlocksList([], $blockContext);

	$this->assertIsArray($list);
	$this->assertNotContains('core/paragraph', $list, "List array does contain core/paragraph item.");
	$this->assertContains('eightshift-boilerplate/button', $list, "List array doesn't contain eightshift-boilerplate/button item.");
	$this->assertContains('core/block', $list, "List array doesn't contain core/block item.");
	$this->assertContains('core/template', $list, "List array doesn't contain core/template item.");
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

	$wrapperManifest = \dirname(__FILE__, 2) . '/data/src/Blocks/wrapper/wrapper.php';

	\ob_start();
	$this->blocksExample->renderWrapperView($wrapperManifest, []);
	$content = \ob_get_clean();

	$this->assertSame('<div>Wrapper!</div>', \trim($content));
});

test('Asserts that renderWrapperView will throw error if path is not valid.', function () {
	$this->blocksExample->renderWrapperView('fake path', []);
})->throws(InvalidBlock::class);

test('Asserts that getCustomCategory will return categories array.', function () {

	$blockContext = mock('WP_Block_Editor_Context');
	$category = $this->blocksExample->getCustomCategory([], $blockContext);

	$this->assertIsArray($category);
	$this->assertContains('eightshift', $category[0], "Items array doesn't contain eightshift category");
});

test('Asserts that getCustomCategory will throw error if first argument is not array.', function () {

	$blockContext = mock('WP_Block_Editor_Context');
	$this->blocksExample->getCustomCategory('', $blockContext);

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

	$this->assertSame(\getenv('EDITOR_COLOR_PALETTE'), 'true', "Method addThemeSupport() didn't add theme support for editor-color-palette");
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

	$this->assertSame(\getenv('BLOCK_TYPE'), 'true', 'Calling void method register_block_type caused no side effects');
});

test('getCustomCategoryOld method will return an array.', function () {
	$post = mock('WP_Post');

	$categoryList = $this->blocksExample->getCustomCategoryOld([], $post);

	$this->assertIsArray($categoryList, 'The result is not an array');
	$this->assertArrayHasKey('slug', $categoryList[0], 'Key slug must be present in the array');
	$this->assertArrayHasKey('title', $categoryList[0], 'Key title must be present in the array');
	$this->assertArrayHasKey('icon', $categoryList[0], 'Key icon must be present in the array');
});

test('filterBlocksContent method will return an array.', function () {
	$this->config
		->shouldReceive('getProjectPath')
		->andReturn('tests/data');

	$parsedBlock = [
		'blockName' => 'eightshift-boilerplate/jumbotron',
		'attrs' =>
			[
				'jumbotronHeadingContent' => 'Some text goes here',
				'jumbotronImageUrl' => 'test.jpeg',
			],
		'innerBlocks' =>
			[
				0 =>
					[
						'blockName' => 'eightshift-boilerplate/description-link',
						'attrs' =>
							[
								'wrapperDisable' => true,
								'descriptionLinkDescriptionLinkIntroContent' => 'Test',
								'descriptionLinkDescriptionLinkIntroSize' => 'regular',
								'descriptionLinkDescriptionLinkParagraphContent' => 'Test',
								'descriptionLinkDescriptionLinkParagraphSize' => 'tiny',
								'descriptionLinkDescriptionLinkImageUrl' => 'test.svg',
								'descriptionLinkDescriptionLinkImageAlt' => 'Check alt text',
								'descriptionLinkDescriptionLinkImageFull' => true,
								'descriptionLinkDescriptionLinkUrl' => 'https://example.com',
								'descriptionLinkDescriptionLinkIsClean' => true,
							],
						'innerBlocks' =>
							[],
						'innerHTML' => '',
						'innerContent' =>
							[],
					],
				1 =>
					[
						'blockName' => 'eightshift-boilerplate/description-link',
						'attrs' =>
							[
								'wrapperDisable' => true,
								'descriptionLinkDescriptionLinkIntroContent' => 'Test',
								'descriptionLinkDescriptionLinkIntroSize' => 'regular',
								'descriptionLinkDescriptionLinkParagraphContent' => 'Content',
								'descriptionLinkDescriptionLinkParagraphSize' => 'tiny',
								'descriptionLinkDescriptionLinkImageUrl' => 'test.svg',
								'descriptionLinkDescriptionLinkImageFull' => true,
								'descriptionLinkDescriptionLinkIsClean' => true,
							],
						'innerBlocks' =>
							[],
						'innerHTML' => '',
						'innerContent' =>
							[],
					],
			],
		'innerHTML' => '',
		'innerContent' =>
			[
				0 => '',
				1 => null,
				2 => '',
				3 => null,
				4 => '',
				5 => null,
				6 => '',
				7 => null,
				8 => '',
			],
	];

	$filteredBlockContent = $this->blocksExample->filterBlocksContent($parsedBlock, []);

	$this->assertIsArray($filteredBlockContent, 'The result is not an array');
});

test('filterBlocksContent method will not filter out the paragraph with content.', function () {
	$this->config
		->shouldReceive('getProjectPath')
		->andReturn('tests/data');

	$parsedBlock = [
		'blockName' => 'eightshift-boilerplate/paragraph',
		'attrs' =>
			[
				'paragraphParagraphContent' => 'Some text goes here',
			],
		'innerBlocks' =>
			'',
		'innerHTML' => '',
		'innerContent' =>
			[
				0 => '',
			],
	];

	$filteredBlockContent = $this->blocksExample->filterBlocksContent($parsedBlock, []);

	$this->assertArrayHasKey('blockName', $filteredBlockContent, 'Key blockName must be present in the array');
	$this->assertArrayHasKey('attrs', $filteredBlockContent, 'Key attrs must be present in the array');
	$this->assertArrayHasKey('paragraphParagraphContent', $filteredBlockContent['attrs'], 'Key paragraphParagraphContent must be present in the attributes array');
});

test('filterBlocksContent method will filter out the paragraph without content.', function () {
	$this->config
		->shouldReceive('getProjectPath')
		->andReturn('tests/data');

	$parsedBlock = [
		'blockName' => 'eightshift-boilerplate/paragraph',
		'attrs' =>
			[
				'paragraphParagraphContent' => '',
			],
		'innerBlocks' => '',
		'innerHTML' => '',
		'innerContent' =>
			[
				0 => '',
			],
		'wrapperDisable' => false
	];

	// Set namespace data.
	$this->blocksExample->getBlocksDataFullRaw();

	$filteredBlockContent = $this->blocksExample->filterBlocksContent($parsedBlock, []);

	$this->assertArrayHasKey('blockName', $filteredBlockContent, 'Key blockName must be present in the array');
	$this->assertArrayHasKey('attrs', $filteredBlockContent, 'Key attrs must be present in the array');
	$this->assertArrayHasKey('wrapperDisable', $filteredBlockContent['attrs'], 'Key wrapperDisable must be present in the attributes array');
	$this->assertArrayHasKey('paragraphUse', $filteredBlockContent['attrs'], 'Key paragraphUse must be present in the attributes array');
	$this->assertTrue($filteredBlockContent['attrs']['wrapperDisable'], 'wrapperDisable must be set to true.');
	$this->assertFalse($filteredBlockContent['attrs']['paragraphUse'], 'paragraphUse must be set to false.');
});
