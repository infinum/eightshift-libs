<?php

/**
 * Tests for PostTrait helper methods.
 *
 * @package EightshiftLibs\Tests\Unit\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\Helpers;

use EightshiftLibs\Tests\BaseTestCase;
use EightshiftLibs\Helpers\PostTrait;
use Brain\Monkey\Functions;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionClass;

/**
 * Wrapper class to test PostTrait methods.
 */
class PostTraitWrapper
{
	use PostTrait;
}

/**
 * Test case for PostTrait utility methods.
 *
 * @coversDefaultClass EightshiftLibs\Helpers\PostTrait
 */
class PostTraitTest extends BaseTestCase
{
	private PostTraitWrapper $wrapper;

	protected function setUp(): void
	{
		parent::setUp();
		$this->wrapper = new PostTraitWrapper();

		// Clear all caches before each test
		$this->clearAllCaches();

		// Mock WordPress functions
		Functions\when('esc_html__')->returnArg(1);
		Functions\when('wp_kses_post')->returnArg(1);
		Functions\when('wp_strip_all_tags')->alias(function ($string) {
			return \strip_tags($string);
		});
	}

	/**
	 * Clear all static caches in PostTrait.
	 */
	private function clearAllCaches(): void
	{
		$reflection = new ReflectionClass(PostTraitWrapper::class);

		$readingTimeCache = $reflection->getProperty('readingTimeCache');
		$readingTimeCache->setAccessible(true);
		$readingTimeCache->setValue(null, []);

		$contentCache = $reflection->getProperty('contentCache');
		$contentCache->setAccessible(true);
		$contentCache->setValue(null, []);

		$postContentCache = $reflection->getProperty('postContentCache');
		$postContentCache->setAccessible(true);
		$postContentCache->setValue(null, []);
	}

	/**
	 * Populate cache with specified number of entries.
	 *
	 * @param string $cacheName Name of cache property
	 * @param int $count Number of entries to add
	 */
	private function populateCache(string $cacheName, int $count): void
	{
		$reflection = new ReflectionClass(PostTraitWrapper::class);
		$cache = $reflection->getProperty($cacheName);
		$cache->setAccessible(true);

		$data = [];
		for ($i = 0; $i < $count; $i++) {
			if ($cacheName === 'readingTimeCache') {
				$data["reading_time_{$i}_200"] = 1;
			} elseif ($cacheName === 'contentCache') {
				$data["content_{$i}"] = 'test content';
			} elseif ($cacheName === 'postContentCache') {
				$data[$i] = 'test content';
			}
		}
		$cache->setValue(null, $data);
	}

	/**
	 * Get current cache size.
	 *
	 * @param string $cacheName Name of cache property
	 * @return int Cache size
	 */
	private function getCacheSize(string $cacheName): int
	{
		$reflection = new ReflectionClass(PostTraitWrapper::class);
		$cache = $reflection->getProperty($cacheName);
		$cache->setAccessible(true);
		$cacheData = $cache->getValue();
		return \count($cacheData);
	}

	/**
	 * @covers ::getReadingTime
	 */
	public function testGetReadingTimeWithValidContent(): void
	{
		$postId = 1;
		$content = '<p>' . \str_repeat('word ', 400) . '</p>'; // 400 words

		Functions\when('get_the_content')->alias(function ($more, $strip, $id) use ($content, $postId) {
			return $id === $postId ? $content : '';
		});

		Functions\when('parse_blocks')->alias(function ($rawContent) {
			return [[
				'blockName' => 'core/paragraph',
				'innerHTML' => $rawContent,
				'innerContent' => [$rawContent]
			]];
		});

		Functions\when('render_block')->alias(function ($block) {
			return $block['innerHTML'] ?? '';
		});

		Functions\when('apply_filters')->alias(function ($hook, $value) {
			return $value;
		});

		// 400 words / 200 words per minute = 2 minutes
		$result = $this->wrapper::getReadingTime($postId, 200);

		$this->assertEquals(2, $result);
	}

	/**
	 * @covers ::getReadingTime
	 */
	public function testGetReadingTimeWithInvalidPostId(): void
	{
		$result = $this->wrapper::getReadingTime(0, 200);

		$this->assertEquals(0, $result);
	}

	/**
	 * @covers ::getReadingTime
	 */
	public function testGetReadingTimeWithNegativePostId(): void
	{
		$result = $this->wrapper::getReadingTime(-5, 200);

		$this->assertEquals(0, $result);
	}

	/**
	 * @covers ::getReadingTime
	 */
	public function testGetReadingTimeWithEmptyContent(): void
	{
		$postId = 2;

		Functions\when('get_the_content')->justReturn('');
		Functions\when('parse_blocks')->justReturn([]);

		$result = $this->wrapper::getReadingTime($postId, 200);

		$this->assertEquals(0, $result);
	}

	/**
	 * @covers ::getReadingTime
	 */
	public function testGetReadingTimeWithCustomAverageWordCount(): void
	{
		$postId = 3;
		$content = '<p>' . \str_repeat('word ', 300) . '</p>'; // 300 words

		Functions\when('get_the_content')->justReturn($content);
		Functions\when('parse_blocks')->alias(function ($rawContent) {
			return [[
				'blockName' => 'core/paragraph',
				'innerHTML' => $rawContent
			]];
		});
		Functions\when('render_block')->alias(function ($block) {
			return $block['innerHTML'] ?? '';
		});
		Functions\when('apply_filters')->alias(function ($hook, $value) {
			return $value;
		});

		// 300 words / 150 words per minute = 2 minutes
		$result = $this->wrapper::getReadingTime($postId, 150);

		$this->assertEquals(2, $result);
	}

	/**
	 * @covers ::getReadingTime
	 */
	public function testGetReadingTimeWithZeroAverageWordCount(): void
	{
		$postId = 4;
		$content = '<p>' . \str_repeat('word ', 400) . '</p>'; // 400 words

		Functions\when('get_the_content')->justReturn($content);
		Functions\when('parse_blocks')->alias(function ($rawContent) {
			return [[
				'blockName' => 'core/paragraph',
				'innerHTML' => $rawContent
			]];
		});
		Functions\when('render_block')->alias(function ($block) {
			return $block['innerHTML'] ?? '';
		});
		Functions\when('apply_filters')->alias(function ($hook, $value) {
			return $value;
		});

		// Should default to 200 words per minute
		$result = $this->wrapper::getReadingTime($postId, 0);

		// 400 / 200 = 2
		$this->assertEquals(2, $result);
	}

	/**
	 * @covers ::getReadingTime
	 */
	public function testGetReadingTimeWithNegativeAverageWordCount(): void
	{
		$postId = 5;
		$content = '<p>' . \str_repeat('word ', 200) . '</p>';

		Functions\when('get_the_content')->justReturn($content);
		Functions\when('parse_blocks')->alias(function ($rawContent) {
			return [[
				'blockName' => 'core/paragraph',
				'innerHTML' => $rawContent
			]];
		});
		Functions\when('render_block')->alias(function ($block) {
			return $block['innerHTML'] ?? '';
		});
		Functions\when('apply_filters')->alias(function ($hook, $value) {
			return $value;
		});

		// Should default to 200 words per minute
		$result = $this->wrapper::getReadingTime($postId, -50);

		// 200 / 200 = 1
		$this->assertEquals(1, $result);
	}

	/**
	 * @covers ::getReadingTime
	 */
	public function testGetReadingTimeRoundsUpPartialMinutes(): void
	{
		$postId = 6;
		// 250 words should round up to 2 minutes (250/200 = 1.25, ceil = 2)
		$content = '<p>' . \str_repeat('word ', 250) . '</p>';

		Functions\when('get_the_content')->justReturn($content);
		Functions\when('parse_blocks')->alias(function ($rawContent) {
			return [[
				'blockName' => 'core/paragraph',
				'innerHTML' => $rawContent
			]];
		});
		Functions\when('render_block')->alias(function ($block) {
			return $block['innerHTML'] ?? '';
		});
		Functions\when('apply_filters')->alias(function ($hook, $value) {
			return $value;
		});

		$result = $this->wrapper::getReadingTime($postId, 200);

		$this->assertEquals(2, $result);
	}

	/**
	 * @covers ::getReadingTime
	 */
	public function testGetReadingTimeWithSmallContent(): void
	{
		$postId = 7;
		// 50 words should be 1 minute (50/200 = 0.25, ceil = 1)
		$content = '<p>' . \str_repeat('word ', 50) . '</p>';

		Functions\when('get_the_content')->justReturn($content);
		Functions\when('parse_blocks')->alias(function ($rawContent) {
			return [[
				'blockName' => 'core/paragraph',
				'innerHTML' => $rawContent
			]];
		});
		Functions\when('render_block')->alias(function ($block) {
			return $block['innerHTML'] ?? '';
		});
		Functions\when('apply_filters')->alias(function ($hook, $value) {
			return $value;
		});

		$result = $this->wrapper::getReadingTime($postId, 200);

		$this->assertEquals(1, $result);
	}

	/**
	 * @covers ::getReadingTime
	 */
	public function testGetReadingTimeWithMultipleBlocks(): void
	{
		$postId = 8;
		$content1 = '<p>' . \str_repeat('word ', 100) . '</p>';
		$content2 = '<p>' . \str_repeat('test ', 100) . '</p>';

		Functions\when('get_the_content')->justReturn($content1 . $content2);
		Functions\when('parse_blocks')->alias(function ($rawContent) use ($content1, $content2) {
			return [
				['blockName' => 'core/paragraph', 'innerHTML' => $content1],
				['blockName' => 'core/paragraph', 'innerHTML' => $content2],
			];
		});
		Functions\when('render_block')->alias(function ($block) {
			return $block['innerHTML'] ?? '';
		});
		Functions\when('apply_filters')->alias(function ($hook, $value) {
			return $value;
		});

		// 200 words total / 200 = 1 minute
		$result = $this->wrapper::getReadingTime($postId, 200);

		$this->assertEquals(1, $result);
	}

	/**
	 * @covers ::getReadingTime
	 */
	public function testGetReadingTimeWithHtmlContent(): void
	{
		$postId = 9;
		$content = '<p>' . \str_repeat('word ', 200) . '</p>';

		Functions\when('get_the_content')->justReturn($content);
		Functions\when('parse_blocks')->alias(function ($rawContent) {
			return [[
				'blockName' => 'core/paragraph',
				'innerHTML' => $rawContent
			]];
		});
		Functions\when('render_block')->alias(function ($block) {
			return $block['innerHTML'] ?? '';
		});
		Functions\when('apply_filters')->alias(function ($hook, $value) {
			return $value;
		});

		// HTML tags should be stripped, 200 words / 200 = 1 minute
		$result = $this->wrapper::getReadingTime($postId, 200);

		$this->assertEquals(1, $result);
	}

	/**
	 * @covers ::getReadingTime
	 */
	#[DataProvider('readingTimeProvider')]
	public function testGetReadingTimeWithVariousWordCounts(int $wordCount, int $avgWordsPerMinute, int $expected, int $postId): void
	{
		$content = '<p>' . \str_repeat('word ', $wordCount) . '</p>';

		Functions\when('get_the_content')->alias(function ($more, $strip, $id) use ($content, $postId) {
			return $id === $postId ? $content : '';
		});
		Functions\when('parse_blocks')->alias(function ($rawContent) {
			return [[
				'blockName' => 'core/paragraph',
				'innerHTML' => $rawContent
			]];
		});
		Functions\when('render_block')->alias(function ($block) {
			return $block['innerHTML'] ?? '';
		});
		Functions\when('apply_filters')->alias(function ($hook, $value) {
			return $value;
		});

		$result = $this->wrapper::getReadingTime($postId, $avgWordsPerMinute);

		$this->assertEquals($expected, $result);
	}

	/**
	 * @covers ::getReadingTime
	 */
	public function testGetReadingTimeCachesBetweenCalls(): void
	{
		$postId = 10;
		$content = '<p>' . \str_repeat('word ', 200) . '</p>';

		$getContentCallCount = 0;
		Functions\when('get_the_content')->alias(function () use ($content, &$getContentCallCount) {
			$getContentCallCount++;
			return $content;
		});
		Functions\when('parse_blocks')->alias(function ($rawContent) {
			return [[
				'blockName' => 'core/paragraph',
				'innerHTML' => $rawContent
			]];
		});
		Functions\when('render_block')->alias(function ($block) {
			return $block['innerHTML'] ?? '';
		});
		Functions\when('apply_filters')->alias(function ($hook, $value) {
			return $value;
		});

		// First call
		$result1 = $this->wrapper::getReadingTime($postId, 200);
		// Second call with same parameters should use cache
		$result2 = $this->wrapper::getReadingTime($postId, 200);

		$this->assertEquals($result1, $result2);
		$this->assertEquals(1, $result1);
	}

	/**
	 * @covers ::getReadingTime
	 */
	public function testGetReadingTimeWithDifferentAverageUsesCache(): void
	{
		$postId = 11;
		$content = '<p>' . \str_repeat('word ', 400) . '</p>';

		Functions\when('get_the_content')->justReturn($content);
		Functions\when('parse_blocks')->alias(function ($rawContent) {
			return [[
				'blockName' => 'core/paragraph',
				'innerHTML' => $rawContent
			]];
		});
		Functions\when('render_block')->alias(function ($block) {
			return $block['innerHTML'] ?? '';
		});
		Functions\when('apply_filters')->alias(function ($hook, $value) {
			return $value;
		});

		// Calculate with 200 wpm
		$result200 = $this->wrapper::getReadingTime($postId, 200);
		// Calculate with 100 wpm - should reuse word count but calculate new time
		$result100 = $this->wrapper::getReadingTime($postId, 100);

		$this->assertEquals(2, $result200); // 400/200 = 2
		$this->assertEquals(4, $result100); // 400/100 = 4
	}

	/**
	 * @covers ::getReadingTime
	 */
	public function testGetReadingTimeWithFalseGetContentReturn(): void
	{
		$postId = 12;

		Functions\when('get_the_content')->justReturn(false);

		$result = $this->wrapper::getReadingTime($postId, 200);

		// Should handle false return from get_the_content
		$this->assertEquals(0, $result);
	}

	/**
	 * @covers ::getReadingTime
	 */
	public function testGetReadingTimeWithNullGetContentReturn(): void
	{
		$postId = 13;

		Functions\when('get_the_content')->justReturn(null);

		$result = $this->wrapper::getReadingTime($postId, 200);

		// Should handle null return from get_the_content
		$this->assertEquals(0, $result);
	}

	/**
	 * @covers ::getReadingTime
	 */
	public function testGetReadingTimeWithEmptyRenderedBlock(): void
	{
		$postId = 14;
		$content = '<p>Some content</p>';

		Functions\when('get_the_content')->justReturn($content);
		Functions\when('parse_blocks')->alias(function () {
			return [['blockName' => 'core/paragraph']];
		});
		Functions\when('render_block')->justReturn(''); // Returns empty string
		Functions\when('apply_filters')->alias(function ($hook, $value) {
			return $value;
		});

		$result = $this->wrapper::getReadingTime($postId, 200);

		// Empty rendered content should result in 0
		$this->assertEquals(0, $result);
	}

	/**
	 * @covers ::getReadingTime
	 */
	public function testGetReadingTimeWithFalseRenderedBlock(): void
	{
		$postId = 15;
		$content = '<p>Some content</p>';

		Functions\when('get_the_content')->justReturn($content);
		Functions\when('parse_blocks')->alias(function () {
			return [['blockName' => 'core/paragraph']];
		});
		Functions\when('render_block')->justReturn(false); // Returns false
		Functions\when('apply_filters')->alias(function ($hook, $value) {
			return $value;
		});

		$result = $this->wrapper::getReadingTime($postId, 200);

		// False rendered content should be skipped
		$this->assertEquals(0, $result);
	}

	/**
	 * @covers ::getReadingTime
	 */
	public function testGetReadingTimeWithEmptyFilteredContent(): void
	{
		$postId = 16;
		$content = '<p>test</p>';

		Functions\when('get_the_content')->justReturn($content);
		Functions\when('parse_blocks')->alias(function ($rawContent) {
			return [[
				'blockName' => 'core/paragraph',
				'innerHTML' => $rawContent
			]];
		});
		Functions\when('render_block')->alias(function ($block) {
			return $block['innerHTML'] ?? '';
		});
		// wp_kses_post returns empty string
		Functions\when('wp_kses_post')->justReturn('');
		Functions\when('apply_filters')->alias(function ($hook, $value) {
			return $value;
		});

		$result = $this->wrapper::getReadingTime($postId, 200);

		// Should handle empty filtered content
		$this->assertEquals(0, $result);
	}

	/**
	 * @covers ::getReadingTime
	 */
	public function testGetReadingTimeWithEmptyStrippedContent(): void
	{
		$postId = 17;
		$content = '<p></p>';

		Functions\when('get_the_content')->justReturn($content);
		Functions\when('parse_blocks')->alias(function ($rawContent) {
			return [[
				'blockName' => 'core/paragraph',
				'innerHTML' => $rawContent
			]];
		});
		Functions\when('render_block')->alias(function ($block) {
			return $block['innerHTML'] ?? '';
		});
		Functions\when('apply_filters')->alias(function ($hook, $value) {
			return $value;
		});
		// wp_strip_all_tags returns empty after stripping
		Functions\when('wp_strip_all_tags')->justReturn('');

		$result = $this->wrapper::getReadingTime($postId, 200);

		// Should handle content that becomes empty after stripping
		$this->assertEquals(0, $result);
	}

	/**
	 * @covers ::getReadingTime
	 */
	public function testGetReadingTimeWithWhitespaceOnlyContent(): void
	{
		$postId = 18;
		$content = '<p>   </p>';

		Functions\when('get_the_content')->justReturn($content);
		Functions\when('parse_blocks')->alias(function ($rawContent) {
			return [[
				'blockName' => 'core/paragraph',
				'innerHTML' => $rawContent
			]];
		});
		Functions\when('render_block')->alias(function ($block) {
			return $block['innerHTML'] ?? '';
		});
		Functions\when('apply_filters')->alias(function ($hook, $value) {
			return $value;
		});
		// After stripping tags, only whitespace remains
		Functions\when('wp_strip_all_tags')->justReturn('   ');

		$result = $this->wrapper::getReadingTime($postId, 200);

		// Whitespace-only content should result in 0
		$this->assertEquals(0, $result);
	}

	/**
	 * @covers ::getReadingTime
	 */
	public function testGetReadingTimeWithMixedContentBlocks(): void
	{
		$postId = 19;
		$content1 = '<p>' . \str_repeat('word ', 100) . '</p>';
		$content2 = '<p>   </p>'; // Empty block
		$content3 = '<p>' . \str_repeat('test ', 100) . '</p>';

		Functions\when('get_the_content')->justReturn($content1 . $content2 . $content3);
		Functions\when('parse_blocks')->alias(function () use ($content1, $content2, $content3) {
			return [
				['blockName' => 'core/paragraph', 'innerHTML' => $content1],
				['blockName' => 'core/paragraph', 'innerHTML' => $content2],
				['blockName' => 'core/paragraph', 'innerHTML' => $content3],
			];
		});
		Functions\when('render_block')->alias(function ($block) {
			return $block['innerHTML'] ?? '';
		});
		Functions\when('apply_filters')->alias(function ($hook, $value) {
			return $value;
		});

		// 200 words total from content1 and content3 / 200 = 1 minute
		$result = $this->wrapper::getReadingTime($postId, 200);

		$this->assertEquals(1, $result);
	}

	/**
	 * @covers ::getReadingTime
	 */
	public function testGetReadingTimeWithExcessiveWhitespace(): void
	{
		$postId = 20;
		$content = '<p>word1    word2\n\n\nword3\t\tword4</p>';

		Functions\when('get_the_content')->justReturn($content);
		Functions\when('parse_blocks')->alias(function ($rawContent) {
			return [[
				'blockName' => 'core/paragraph',
				'innerHTML' => $rawContent
			]];
		});
		Functions\when('render_block')->alias(function ($block) {
			return $block['innerHTML'] ?? '';
		});
		Functions\when('apply_filters')->alias(function ($hook, $value) {
			return $value;
		});

		// 4 words with excessive whitespace, should be normalized
		$result = $this->wrapper::getReadingTime($postId, 200);

		$this->assertEquals(1, $result);
	}

	/**
	 * @covers ::getReadingTime
	 */
	public function testGetReadingTimeWithSingleSpaceAfterTrim(): void
	{
		$postId = 21;
		// Content that results in single space after processing
		$content = '<p> </p>';

		Functions\when('get_the_content')->justReturn($content);
		Functions\when('parse_blocks')->alias(function ($rawContent) {
			return [[
				'blockName' => 'core/paragraph',
				'innerHTML' => $rawContent
			]];
		});
		Functions\when('render_block')->alias(function ($block) {
			return $block['innerHTML'] ?? '';
		});
		Functions\when('apply_filters')->alias(function ($hook, $value) {
			return $value;
		});
		// Return content that becomes single space after trim and preg_replace
		Functions\when('wp_strip_all_tags')->justReturn(' ');

		$result = $this->wrapper::getReadingTime($postId, 200);

		// Single space should be ignored, resulting in 0
		$this->assertEquals(0, $result);
	}

	/**
	 * @covers ::getReadingTime
	 */
	public function testGetReadingTimePostContentCacheHit(): void
	{
		$postId = 22;
		$content = '<p>' . \str_repeat('word ', 100) . '</p>';

		$getContentCalls = 0;
		Functions\when('get_the_content')->alias(function () use ($content, &$getContentCalls) {
			$getContentCalls++;
			return $content;
		});
		Functions\when('parse_blocks')->alias(function ($rawContent) {
			return [[
				'blockName' => 'core/paragraph',
				'innerHTML' => $rawContent
			]];
		});
		Functions\when('render_block')->alias(function ($block) {
			return $block['innerHTML'] ?? '';
		});
		Functions\when('apply_filters')->alias(function ($hook, $value) {
			return $value;
		});

		// First call - populates all caches
		$result1 = $this->wrapper::getReadingTime($postId, 200);
		$this->assertEquals(1, $result1);
		$this->assertEquals(1, $getContentCalls);

		// Second call with different average - should hit post content cache
		$result2 = $this->wrapper::getReadingTime($postId, 100);
		$this->assertEquals(1, $result2);
		// Should still be 1 call because cache was hit
		$this->assertEquals(1, $getContentCalls);
	}

	/**
	 * @covers ::getReadingTime
	 */
	public function testGetReadingTimeContentCacheHit(): void
	{
		$postId = 23;
		$content = '<p>' . \str_repeat('word ', 300) . '</p>';

		$parseBlocksCalls = 0;
		Functions\when('get_the_content')->justReturn($content);
		Functions\when('parse_blocks')->alias(function ($rawContent) use (&$parseBlocksCalls) {
			$parseBlocksCalls++;
			return [[
				'blockName' => 'core/paragraph',
				'innerHTML' => $rawContent
			]];
		});
		Functions\when('render_block')->alias(function ($block) {
			return $block['innerHTML'] ?? '';
		});
		Functions\when('apply_filters')->alias(function ($hook, $value) {
			return $value;
		});

		// First call
		$result1 = $this->wrapper::getReadingTime($postId, 150);
		$this->assertEquals(2, $result1);
		$this->assertEquals(1, $parseBlocksCalls);

		// Second call with different average - should NOT parse blocks again
		$result2 = $this->wrapper::getReadingTime($postId, 300);
		$this->assertEquals(1, $result2);
		// parse_blocks should still be called only once due to content cache
		$this->assertEquals(1, $parseBlocksCalls);
	}

	/**
	 * @covers ::getReadingTime
	 */
	public function testGetReadingTimeDoesNotCacheWhenLimitReached(): void
	{
		// Pre-populate reading time cache to 1000 entries (at limit)
		$this->populateCache('readingTimeCache', 1000);

		$postId = 9999;
		$content = '<p>' . \str_repeat('word ', 200) . '</p>';

		Functions\when('get_the_content')->justReturn($content);
		Functions\when('parse_blocks')->alias(function ($rawContent) {
			return [[
				'blockName' => 'core/paragraph',
				'innerHTML' => $rawContent
			]];
		});
		Functions\when('render_block')->alias(function ($block) {
			return $block['innerHTML'] ?? '';
		});
		Functions\when('apply_filters')->alias(function ($hook, $value) {
			return $value;
		});

		$result = $this->wrapper::getReadingTime($postId, 200);

		// Should still calculate correctly
		$this->assertEquals(1, $result);
		// Cache size should remain at 1000 (not increase)
		$this->assertEquals(1000, $this->getCacheSize('readingTimeCache'));
	}

	/**
	 * @covers ::getReadingTime
	 */
	public function testGetReadingTimeContentCacheLimit(): void
	{
		// Pre-populate content cache to 500 entries (at limit)
		$this->populateCache('contentCache', 500);

		$postId = 8888;
		$content = '<p>' . \str_repeat('word ', 100) . '</p>';

		Functions\when('get_the_content')->justReturn($content);
		Functions\when('parse_blocks')->alias(function ($rawContent) {
			return [[
				'blockName' => 'core/paragraph',
				'innerHTML' => $rawContent
			]];
		});
		Functions\when('render_block')->alias(function ($block) {
			return $block['innerHTML'] ?? '';
		});
		Functions\when('apply_filters')->alias(function ($hook, $value) {
			return $value;
		});

		$result = $this->wrapper::getReadingTime($postId, 200);

		// Should still calculate correctly
		$this->assertEquals(1, $result);
		// Content cache should remain at 500 (not increase)
		$this->assertEquals(500, $this->getCacheSize('contentCache'));
	}

	/**
	 * @covers ::getReadingTime
	 */
	public function testGetReadingTimePostContentCacheLimit(): void
	{
		// Pre-populate post content cache to 200 entries (at limit)
		$this->populateCache('postContentCache', 200);

		$postId = 7777;
		$content = '<p>' . \str_repeat('word ', 100) . '</p>';

		Functions\when('get_the_content')->justReturn($content);
		Functions\when('parse_blocks')->alias(function ($rawContent) {
			return [[
				'blockName' => 'core/paragraph',
				'innerHTML' => $rawContent
			]];
		});
		Functions\when('render_block')->alias(function ($block) {
			return $block['innerHTML'] ?? '';
		});
		Functions\when('apply_filters')->alias(function ($hook, $value) {
			return $value;
		});

		$result = $this->wrapper::getReadingTime($postId, 200);

		// Should still calculate correctly
		$this->assertEquals(1, $result);
		// Post content cache should remain at 200 (not increase)
		$this->assertEquals(200, $this->getCacheSize('postContentCache'));
	}

	/**
	 * Data providers
	 */
	public static function readingTimeProvider(): array
	{
		return [
			'100 words at 200 wpm' => [100, 200, 1, 100],
			'200 words at 200 wpm' => [200, 200, 1, 200],
			'400 words at 200 wpm' => [400, 200, 2, 400],
			'600 words at 200 wpm' => [600, 200, 3, 600],
			'300 words at 100 wpm' => [300, 100, 3, 300],
			'500 words at 250 wpm' => [500, 250, 2, 500],
			'1000 words at 200 wpm' => [1000, 200, 5, 1000],
		];
	}
}
