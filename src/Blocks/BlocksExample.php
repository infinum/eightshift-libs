<?php

/**
 * Class Blocks is the base class for Gutenberg blocks registration.
 * It provides the ability to register custom blocks using manifest.json.
 *
 * @package EightshiftBoilerplate\Blocks
 */

declare(strict_types=1);

namespace EightshiftBoilerplate\Blocks;

use EightshiftLibs\Blocks\AbstractBlocks;

/**
 * Class Blocks
 */
class BlocksExample extends AbstractBlocks
{
	/**
	 * Reusable blocks Capability Name.
	 */
	public const REUSABLE_BLOCKS_CAPABILITY = 'edit_reusable_blocks';

	/**
	 * Register all the hooks
	 *
	 * @return void
	 */
	public function register(): void
	{
		// Register all custom blocks.
		\add_action('init', [$this, 'getBlocksDataFullRaw'], 10);
		\add_action('init', [$this, 'registerBlocks'], 11);

		// Remove P tags from content.
		\remove_filter('the_content', 'wpautop');

		// Create new custom category for custom blocks.
		if (\is_wp_version_compatible('5.8')) {
			\add_filter('block_categories_all', [$this, 'getCustomCategory'], 10, 2);
		} else {
			\add_filter('block_categories', [$this, 'getCustomCategoryOld'], 10, 2);
		}

		// Register custom theme support options.
		\add_action('after_setup_theme', [$this, 'addThemeSupport'], 25);

		// Register custom project color palette.
		\add_action('after_setup_theme', [$this, 'changeEditorColorPalette'], 11);

		// Register Reusable blocks side menu.
		\add_action('admin_menu', [$this, 'addReusableBlocks']);

		// Filter block content.
		\add_filter('render_block_data', [$this, 'filterBlocksContent'], 10, 2);
	}

	/**
	 * Add Reusable Blocks as a part of a sidebar menu.
	 *
	 * @return void
	 */
	public function addReusableBlocks(): void
	{
		\add_menu_page(
			\esc_html__('Blocks', 'eightshift-libs'),
			\esc_html__('Blocks', 'eightshift-libs'),
			self::REUSABLE_BLOCKS_CAPABILITY,
			'edit.php?post_type=wp_block',
			'', // @phpstan-ignore-line
			'dashicons-editor-table',
			4
		);
	}
}
