<?php

namespace Tests\Unit\Blocks;

use Brain\Monkey\Functions;
use EightshiftBoilerplate\Blocks\BlocksExample;
use EightshiftLibs\Exception\InvalidBlock;
use EightshiftLibs\Helpers\Components;
use WP_Block_Editor_Context;

use function Tests\buildTestBlocks;
use function Tests\mock;

beforeEach(function() {
	$this->mock = new BlocksExample();
});

afterEach(function() {
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

test('Asserts that getAllBlocksList is not influenced by the first parameter', function ($argument) {

	$blockContext = mock(WP_Block_Editor_Context::class);
	$blockContext->post = null;

	buildTestBlocks();

	Components::setConfigFlags();

	$blocks = $this->mock->getAllBlocksList($argument, $blockContext);

	expect($blocks)
		->toBeArray()
		->not->toContain('core/paragraph')
		->not->toContain('test')
		->toContain('eightshift-boilerplate/button', 'eightshift-boilerplate/heading', 'core/block', 'core/template');

})->with('getAllAllowedBlocksListAllTypesArguments');

test('Asserts that getAllAllowedBlocksList will return true if post type is eightshift-forms for WP 5.8.', function () {

	$blockContext = mock('WP_Block_Editor_Context');
	$blockContext->post = mock('WP_Post');
	$blockContext->post->post_type = 'eightshift-forms';

	$blocks = $this->mock->getAllAllowedBlocksList([], $blockContext);

	expect($blocks)
		->toBeTrue();
});

test('Asserts that getAllAllowedBlocksList first argument is bool and return first argument for WP 5.8.', function () {

	$blockContext = mock('WP_Block_Editor_Context');
	$blockContext->post = mock('WP_Post');
	$blockContext->post->post_type = 'post';

	$blocks = $this->mock->getAllAllowedBlocksList(true, $blockContext);

	expect($blocks)->toBeTrue();

	$blocks = $this->mock->getAllAllowedBlocksList(false, $blockContext);

	expect($blocks)->toBeFalse();
});

test('Asserts that getAllAllowedBlocksList first argument is not bool and returns list with appended project blocks to the first argument', function () {

	$blockContext = mock('WP_Block_Editor_Context');
	$blockContext->post = mock('WP_Post');
	$blockContext->post->post_type = 'post';

	buildTestBlocks();

	$blocks = $this->mock->getAllAllowedBlocksList(['test'], $blockContext);

	expect($blocks)
		->toBeArray()
		->not->toContain('core/paragraph')
		->toContain('test', 'eightshift-boilerplate/button', 'core/block', 'core/template');
});

test('Asserts that getAllAllowedBlocksList will return projects blocks and passed blocks for WP 5.8.', function () {
	$blockContext = mock(WP_Block_Editor_Context::class);
	$blockContext->post = null;

	buildTestBlocks();

	Components::setConfigFlags();

	$blocks = $this->mock->getAllAllowedBlocksList(['test'], $blockContext);

	expect($blocks)
		->toBeArray()
		->toContain('eightshift-boilerplate/button', 'eightshift-boilerplate/heading', 'core/block', 'core/template', 'test');
});

test('Asserts that getAllAllowedBlocksList will return only projects blocks for WP 5.8.', function () {
	$blockContext = mock(WP_Block_Editor_Context::class);
	$blockContext->post = null;

	buildTestBlocks();

	Components::setConfigFlags();

	$blocks = $this->mock->getAllAllowedBlocksList([], $blockContext);

	expect($blocks)
		->toBeArray()
		->toContain('eightshift-boilerplate/button', 'eightshift-boilerplate/heading', 'core/block', 'core/template');
});

test('Asserts that render component will load view template.', function () {

	$blockManifest = [
		'blockName' => 'button',
	];

	buildTestBlocks();

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

	buildTestBlocks();

	$this->mock->render($blockManifest, '');
})->throws(InvalidBlock::class);

test('Asserts that render will throw error if wrapper view is missing.', function () {

	$blockManifest = [
		'blockName' => 'fake',
	];

	buildTestBlocks();

	$this->mock->render($blockManifest, '');

})->throws(InvalidBlock::class);

test('Asserts that renderWrapperView will return a valid file.', function () {

	buildTestBlocks();

	$wrapperFile = Components::getProjectPaths('blocksDestinationWrapper', 'wrapper.php');

	\ob_start();
	$this->mock->renderWrapperView($wrapperFile, []);
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

	buildTestBlocks();

	$this->mock->changeEditorColorPalette();

	expect(\getenv('EDITOR_COLOR_PALETTE'))->toBe('true');
});

test('registerBlocks method will register all blocks.', function () {

	putenv('BLOCK_TYPE=false');

	Functions\when('register_block_type')->alias(function(string $name, array $args = []) {
		putenv('BLOCK_TYPE=true');
	});

	buildTestBlocks();

	$this->mock->registerBlocks();

	expect(\getenv('BLOCK_TYPE'))->toBe('true');
});

test('filterBlocksContent method will return an array.', function () {

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
	buildTestBlocks();

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
