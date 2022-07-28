<?php

/**
 * Trait TraitBlocksLegacy provides all legacy methods used for back compatibility.
 *
 * @package EightshiftLibs\Blocks
 */

declare(strict_types=1);

namespace EightshiftLibs\Blocks;

use EightshiftLibs\Helpers\Components;
use WP_Post;

/**
 * Trait TraitBlocksLegacy
 */
trait TraitBlocksLegacy
{
	/**
	 * Get all blocks with full block name - legacy.
	 *
	 * Used to limit what blocks are going to be used in your project using allowed_block_types filter.
	 *
	 * @hook allowed_block_types This is a WP 5 - WP 5.7 compatible hook callback. Will not work with WP 5.8!
	 *
	 * @param bool|string[] $allowedBlockTypes Array of block type slugs, or boolean to enable/disable all.
	 * @param WP_Post $post The post resource data.
	 *
	 * @return bool|string[] Boolean if you want to disallow or allow all blocks, or a list of allowed blocks.
	 */
	public function getAllBlocksListOld($allowedBlockTypes, WP_Post $post)
	{
		if (\gettype($allowedBlockTypes) === 'boolean') {
			return $allowedBlockTypes;
		}

		$allowedBlockTypes = \array_map(
			function ($block) {
				return $block['blockFullName'];
			},
			Components::getBlocks()
		);

		// Allow reusable block.
		$allowedBlockTypes[] = 'core/block';
		$allowedBlockTypes[] = 'core/template';

		return $allowedBlockTypes;
	}

	/**
	 * Create custom category to assign all custom blocks - legacy.
	 *
	 * This category will be shown on all blocks list in "Add Block" button.
	 *
	 * @hook block_categories This is a WP 5 - WP 5.7 compatible hook callback. Will not work with WP 5.8!
	 *
	 * @param array<int, array<string, string|null>> $categories Array of categories for block types.
	 * @param WP_Post $post Post being loaded.
	 *
	 * @return array<int, array<string, string|null>> Array of categories for block types.
	 */
	public function getCustomCategoryOld(array $categories, WP_Post $post): array
	{
		return \array_merge(
			$categories,
			[
				[
					'slug' => 'eightshift',
					'title' => \esc_html__('Eightshift', 'eightshift-libs'),
					'icon' => 'admin-settings',
				],
			]
		);
	}
}
