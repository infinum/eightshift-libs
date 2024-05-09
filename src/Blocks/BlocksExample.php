<?php

/**
 * Class Blocks is the base class for Gutenberg blocks registration.
 * It provides the ability to register custom blocks using manifest.json.
 *
 * @package %g_namespace%\Blocks
 */

declare(strict_types=1);

namespace %g_namespace%\Blocks;

use %g_use_libs%\Blocks\AbstractBlocks;

/**
 * Class Blocks
 */
class BlocksExample extends AbstractBlocks
{
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
		\add_filter('block_categories_all', [$this, 'getCustomCategory'], 10, 2);

		// Register custom theme support options.
		\add_action('after_setup_theme', [$this, 'addThemeSupport'], 25);

		// Register custom project color palette.
		\add_action('after_setup_theme', [$this, 'changeEditorColorPalette'], 11);

		// Filter block content.
		\add_filter('render_block_data', [$this, 'filterBlocksContent'], 10, 2);

		// Output inline css variables.
		\add_action('wp_footer', [$this, 'outputCssVariablesInline']);
	}
}
