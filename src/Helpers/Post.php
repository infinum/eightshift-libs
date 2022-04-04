<?php

/**
 * Helpers for post.
 *
 * @package EightshiftLibs\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Helpers;

/**
 * Class PostTrait helper.
 */
trait PostTrait
{
	/**
	 * Return content reading time
	 *
	 * This helper will parse blocks, strip all the empty spaces and
	 * HTML tags, and count the words in the string.
	 *
	 * We take that the average reading speed is 200 words per minute.
	 * The rest is math :D.
	 *
	 * @param int $postID ID of post content to calculate.
	 * @param int $averageWordCount Average reading speed.
	 *
	 * @return int reading time integer.
	 */
	public static function getReadingTime(int $postID, int $averageWordCount = 200): int
	{
		$contentBlocks = \parse_blocks(\get_the_content(null, false, $postID));

		$contentBlocksRendered = \array_map(
			function ($block) {
				return \wp_kses_post(\apply_filters('the_content', \render_block($block)));
			},
			$contentBlocks
		);

		$contentBlocksCleaned = \array_map(
			function ($item) {
				return \preg_replace('/\s+/', ' ', \trim($item));
			},
			$contentBlocksRendered
		);

		// Remove arrays with empty strings. These are usually remnants from returns (spaces).
		$content = \array_filter($contentBlocksCleaned, function ($str) {
			return $str !== "";
		});

		$wordCount = \str_word_count(\wp_strip_all_tags(\implode('', $content)));
		$readingTime = \ceil($wordCount / $averageWordCount);

		/* translators: %d: number of minutes needed for reading the article. */
		return (int)$readingTime;
	}
}
