<?php

/**
 * The object helper specific functionality inside classes.
 * Used in admin or theme side but only inside a class.
 *
 * @package EightshiftLibs\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Helpers;

/**
 * Post class helper
 */
class Post
{
	/**
	 * Average reading speed.
	 *
	 * @var int
	 */
	public const AVERAGE_WORD_COUNT = 200;

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
	 *
	 * @return int reading time integer.
	 */
	public static function getReadingTime(int $postID): int
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
		$readingTime = \ceil($wordCount / self::AVERAGE_WORD_COUNT);

		/* translators: %d: number of minutes needed for reading the article. */
		return (int)$readingTime;
	}
}
