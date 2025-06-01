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
	 * Cache for reading time calculations to avoid repeated processing.
	 *
	 * @var array<string, int>
	 */
	private static array $readingTimeCache = [];

	/**
	 * Cache for parsed and cleaned content to avoid repeated processing.
	 *
	 * @var array<string, string>
	 */
	private static array $contentCache = [];

	/**
	 * Cache for post content to avoid repeated WordPress calls.
	 *
	 * @var array<int, string>
	 */
	private static array $postContentCache = [];

	/**
	 * Return content reading time with optimized performance.
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
		// Early return for invalid inputs
		if ($postID <= 0) {
			return 0;
		}

		// Normalize average word count to prevent division by zero
		if ($averageWordCount <= 0) {
			$averageWordCount = 200;
		}

		// Create cache key for this specific calculation
		$cacheKey = "reading_time_{$postID}_{$averageWordCount}";

		// Check cache first for exact same calculation
		if (isset(self::$readingTimeCache[$cacheKey])) {
			return self::$readingTimeCache[$cacheKey];
		}

		// Get word count (cached separately for reuse with different average speeds)
		$wordCount = self::getPostWordCount($postID);

		// Early return if no content
		if ($wordCount === 0) {
			self::$readingTimeCache[$cacheKey] = 0;
			return 0;
		}

		// Calculate reading time
		$readingTime = (int) \ceil($wordCount / $averageWordCount);

		// Cache the result (limit cache size to prevent memory bloat)
		if (\count(self::$readingTimeCache) < 1000) {
			self::$readingTimeCache[$cacheKey] = $readingTime;
		}

		return $readingTime;
	}

	/**
	 * Get word count for a post with optimized caching and processing.
	 *
	 * @param int $postID Post ID to analyze.
	 *
	 * @return int Word count.
	 */
	private static function getPostWordCount(int $postID): int
	{
		// Check content cache first
		$contentCacheKey = "content_{$postID}";
		if (isset(self::$contentCache[$contentCacheKey])) {
			return \str_word_count(self::$contentCache[$contentCacheKey]);
		}

		// Get raw post content with caching
		$rawContent = self::getRawPostContent($postID);
		if ($rawContent === '') {
			self::$contentCache[$contentCacheKey] = '';
			return 0;
		}

		// Parse and process content efficiently
		$cleanedContent = self::processPostContent($rawContent);

		// Cache the cleaned content (limit cache size)
		if (\count(self::$contentCache) < 500) {
			self::$contentCache[$contentCacheKey] = $cleanedContent;
		}

		return \str_word_count($cleanedContent);
	}

	/**
	 * Get raw post content with caching to avoid repeated WordPress calls.
	 *
	 * @param int $postID Post ID.
	 *
	 * @return string Raw post content.
	 */
	private static function getRawPostContent(int $postID): string
	{
		// Check cache first
		if (isset(self::$postContentCache[$postID])) {
			return self::$postContentCache[$postID];
		}

		// Get content from WordPress
		$content = \get_the_content(null, false, $postID);
		if (!$content) {
			$content = '';
		}

		// Cache the result (limit cache size)
		if (\count(self::$postContentCache) < 200) {
			self::$postContentCache[$postID] = $content;
		}

		return $content;
	}

	/**
	 * Process post content efficiently in a single pass.
	 *
	 * @param string $rawContent Raw post content.
	 *
	 * @return string Cleaned content ready for word counting.
	 */
	private static function processPostContent(string $rawContent): string
	{
		// Early return for empty content
		if ($rawContent === '') {
			return '';
		}

		// Parse blocks once
		$contentBlocks = \parse_blocks($rawContent);

		// Early return if no blocks
		if (empty($contentBlocks)) {
			return '';
		}

		// Process blocks in a single efficient pass
		$cleanedParts = [];

		foreach ($contentBlocks as $block) {
			// Render block and apply filters
			$rendered = \render_block($block);
			if (!$rendered) {
				continue;
			}

			// Apply content filters and sanitize
			$filtered = \wp_kses_post(\apply_filters('the_content', $rendered));
			if ($filtered === '') {
				continue;
			}

			// Strip all HTML tags
			$stripped = \wp_strip_all_tags($filtered);
			if ($stripped === '') {
				continue;
			}

			// Clean whitespace efficiently
			$cleaned = \preg_replace('/\s+/', ' ', \trim($stripped));
			if ($cleaned !== '' && $cleaned !== ' ') {
				$cleanedParts[] = $cleaned;
			}
		}

		// Join all parts with single space
		return \implode(' ', $cleanedParts);
	}
}
