<?php

/**
 * Helpers for reusable blocks.
 *
 * @package EightshiftLibs\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Helpers;

/**
 * Trait ReusableBlockTrait helper.
 */
trait ReusableBlockTrait
{
	/**
	 * Render reusable blocks content.
	 *
	 * This helper will get reusable blocks content and render it.
	 *
	 * @param int $commonContent ID of the reusable block content to render.
	 *
	 * @return string Rendered content.
	 */
	public static function renderReusableBlock(int $commonContent): string
	{
		$blocks = \parse_blocks(\get_the_content(null, false, $commonContent));

		$renderedContent = '';

		foreach ($blocks as $block) {
			$renderedContent .= \apply_filters('the_content', \render_block($block));
		}

		return $renderedContent;
	}
}
