<?php

namespace Tests\Unit\Blocks;

use Brain\Monkey;
use Brain\Monkey\Functions;
use EightshiftBoilerplate\Blocks\BlocksExample;
use EightshiftLibs\Exception\InvalidBlock;
use EightshiftLibs\Helpers\Components;
use WP_Block_Editor_Context;

use function Tests\mock;
use function Tests\setAfterEach;
use function Tests\setBeforeEach;
use function Tests\setupMocks;

beforeEach(function() {
	setBeforeEach();

	// $this->config = mock('alias:EightshiftBoilerplate\Config\Config');

	$this->mock = new BlocksExample();
});

afterEach(function() {
	setAfterEach();

	unset($this->mock);
});

test('Register method will call all register hooks', function () {
	$this->mock->register();

	expect(has_filter('block_categories_all', 'EightshiftBoilerplate\Blocks\BlocksExample->getCustomCategory()'))->toBe(10);
	expect(has_action('init', 'EightshiftBoilerplate\Blocks\BlocksExample->getBlocksDataFullRaw()'))->toBe(10);
	expect(has_action('init', 'EightshiftBoilerplate\Blocks\BlocksExample->registerBlocks()'))->toBe(11);
	expect(has_action('after_setup_theme', 'EightshiftBoilerplate\Blocks\BlocksExample->addThemeSupport()'))->toBe(25);
	expect(has_action('after_setup_theme', 'EightshiftBoilerplate\Blocks\BlocksExample->changeEditorColorPalette()'))->toBe(11);
});

test('addThemeSupport method will call add_theme_support() function with different arguments', function () {

	Functions\when('add_theme_support')->alias(function($arg) {
		$envName = strtoupper($arg);
		$envName = \str_replace('-', '_', $envName);
		putenv("{$envName}=true");
	});

	$this->mock->addThemeSupport();

	expect(\getenv('ALIGN_WIDE'))->toBe('true');
});

test('Asserts that getAllBlocksList first argument is boolean and return the provided attribute as return value for older WP versions.', function () {

	Functions\when('is_wp_version_compatible')->justReturn(false);

	$post = mock('WP_Post');

	$blocks = $this->mock->getAllBlocksListOld(true, $post);

	expect($blocks)->toBeTrue();

	$blocks = $this->mock->getAllBlocksListOld(false, $post);

	expect($blocks)->toBeFalse();
});

test('Asserts that getAllBlocksList will return true if post type is eightshift-forms for WP 5.8.', function () {

	$blockContext = mock('WP_Block_Editor_Context');
	$blockContext->post = mock('WP_Post');
	$blockContext->post->post_type = 'eightshift-forms';

	$blocks = $this->mock->getAllBlocksList([], $blockContext);

	expect($blocks)
		->toBeTrue();
});

test('Asserts that getAllBlocksList first argument is not bool and return first argument for WP 5.8.', function () {

	$blockContext = mock('WP_Block_Editor_Context');
	$blockContext->post = mock('WP_Post');
	$blockContext->post->post_type = 'post';

	$blocks = $this->mock->getAllBlocksList(['test'], $blockContext);

	expect($blocks)
		->toBeArray()
		->toContain('test');
});

test('Asserts that getAllBlocksListOld will return only projects blocks for older versions.', function () {

	Functions\when('is_wp_version_compatible')->justReturn(false);

	$post = mock(\WP_Post::class);

	$this->mock->getBlocksDataFullRaw();

	$list = $this->mock->getAllBlocksListOld([], $post);

	expect($list)
		->toBeArray()
		->not->toContain('core/paragraph')
		->toContain('eightshift-boilerplate/button')
		->toContain('core/block')
		->toContain('core/template');
});

test('Asserts that getAllBlocksList will return only projects blocks for WP 5.8.', function () {

	$blockContext = mock(WP_Block_Editor_Context::class);
	$blockContext->post = null;

	(new BlocksExample())->getBlocksDataFullRaw();

	Components::setConfigFlags();

	$blocks = $this->mock->getAllBlocksList(false, $blockContext);

	expect($blocks)
		->toBeArray()
		->toContain('eightshift-boilerplate/button', 'eightshift-boilerplate/heading', 'core/block', 'core/template');
});

test('Asserts that render component will load view template.', function () {

	$blockManifest = [
		'blockName' => 'button',
	];

	$this->config
		->shouldReceive('getProjectPath')
		->andReturn('tests/data');

	$block = $this->mock->render($blockManifest, '');

	expect($block)
		->toBeString()
		->toContain('Wrapper!')
		->not->toContain('fake');
});

test('Asserts that render will throw error if block view is missing.', function () {

	$blockManifest = [
		'blockName' => 'fake',
	];

	$this->config
		->shouldReceive('getProjectPath')
		->andReturn('tests/data');

	$this->mock->render($blockManifest, '');
})->throws(InvalidBlock::class);

test('Asserts that render will throw error if wrapper view is missing.', function () {

	$blockManifest = [
		'blockName' => 'fake',
	];

	$this->config
		->shouldReceive('getProjectPath')
		->andReturn('fake');

	$this->mock->render($blockManifest, '');

})->throws(InvalidBlock::class);

test('Asserts that renderWrapperView will return a valid file.', function () {

	$wrapperManifest = \dirname(__FILE__, 2) . '/data/src/Blocks/wrapper/wrapper.php';

	\ob_start();
	$this->mock->renderWrapperView($wrapperManifest, []);
	$content = \ob_get_clean();

	expect(\trim($content))
		->toBeString()
		->toBe('<div>Wrapper!</div>');
});

test('Asserts that renderWrapperView will throw error if path is not valid.', function () {
	$this->mock->renderWrapperView('fake path', []);
})->throws(InvalidBlock::class);

test('Asserts that getCustomCategory will return categories array.', function () {

	$blockContext = mock('WP_Block_Editor_Context');
	$category = $this->mock->getCustomCategory([], $blockContext);

	expect($category)->toBeArray();
	
	expect($category[0])
		->toBeArray()
		->toContain('eightshift');
});

test('Asserts that getCustomCategory will throw error if first argument is not array.', function () {

	$blockContext = mock('WP_Block_Editor_Context');
	$this->mock->getCustomCategory('', $blockContext);

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

	$this->mock->getBlocksDataFullRaw();

	$this->mock->changeEditorColorPalette();

	expect(\getenv('EDITOR_COLOR_PALETTE'))->toBe('true');
});

test('registerBlocks method will register all blocks.', function () {

	putenv('BLOCK_TYPE=false');

	Functions\when('register_block_type')->alias(function(string $name, array $args = []) {
		putenv('BLOCK_TYPE=true');
	});

	$this->config
		->shouldReceive('getProjectPath')
		->andReturn('tests/data');

	$this->mock->getBlocksDataFullRaw();

	$this->mock->registerBlocks();

	expect(\getenv('BLOCK_TYPE'))->toBe('true');
});

test('getCustomCategoryOld method will return an array.', function () {
	$post = mock('WP_Post');

	$categoryList = $this->mock->getCustomCategoryOld([], $post);

	expect($categoryList)->toBeArray();
	
	expect($categoryList[0])
		->toBeArray()
		->toHaveKey('slug')
		->toHaveKey('title')
		->toHaveKey('icon');
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

	$filteredBlockContent = $this->mock->filterBlocksContent($parsedBlock, []);

	expect($filteredBlockContent)->toBeArray();
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

	$filteredBlockContent = $this->mock->filterBlocksContent($parsedBlock, []);

	expect($filteredBlockContent)
		->toBeArray()
		->toHaveKey('blockName')
		->toHaveKey('attrs');

	expect($filteredBlockContent['attrs'])
		->toBeArray()
		->toHaveKey('paragraphParagraphContent');
});

test('filterBlocksContent method will filter out the paragraph without content.', function () {
	$this->config
		->shouldReceive('getProjectPath')
		->andReturn('tests/data');

	$parsedBlock = [
		'blockName' => 'eightshift-boilerplate/paragraph',
		'attrs' => [
			'paragraphParagraphContent' => '',
			'wrapperDisable' => true,
			'paragraphUse' => false,
		],
		'innerBlocks' => '',
		'innerHTML' => '',
		'innerContent' => [
			0 => '',
		],
	];

	// Set namespace data.
	$this->mock->getBlocksDataFullRaw();

	$filteredBlockContent = $this->mock->filterBlocksContent($parsedBlock, []);

	expect($filteredBlockContent)
		->toBeArray()
		->toHaveKey('blockName')
		->toHaveKey('attrs');

	expect($filteredBlockContent['attrs'])
		->toBeArray()
		->toHaveKey('wrapperDisable')
		->toHaveKey('paragraphUse');

	expect($filteredBlockContent['attrs']['wrapperDisable'])->toBeTrue();
	expect($filteredBlockContent['attrs']['paragraphUse'])->toBeFalse();
});
