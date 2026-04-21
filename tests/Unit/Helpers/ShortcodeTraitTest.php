<?php

/**
 * Tests for ShortcodeTrait helper methods.
 *
 * @package EightshiftLibs\Tests\Unit\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\Helpers;

use EightshiftLibs\Tests\BaseTestCase;
use EightshiftLibs\Helpers\ShortcodeTrait;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Wrapper class to test ShortcodeTrait methods.
 */
class ShortcodeTraitWrapper
{
	use ShortcodeTrait;
}

/**
 * Test case for ShortcodeTrait utility methods.
 *
 * @coversDefaultClass EightshiftLibs\Helpers\ShortcodeTrait
 */
class ShortcodeTraitTest extends BaseTestCase
{
	private ShortcodeTraitWrapper $wrapper;

	protected function setUp(): void
	{
		parent::setUp();
		$this->wrapper = new ShortcodeTraitWrapper();

		// Reset global shortcode tags
		global $shortcode_tags;
		$shortcode_tags = [];
	}

	protected function tearDown(): void
	{
		// Clean up global state
		global $shortcode_tags;
		$shortcode_tags = [];

		parent::tearDown();
	}

	/**
	 * @covers ::getShortcode
	 */
	public function testGetShortcodeWithValidShortcode(): void
	{
		global $shortcode_tags;

		// Register a test shortcode
		$shortcode_tags['test_shortcode'] = function ($atts, $content, $tag) {
			return 'Shortcode executed';
		};

		$result = $this->wrapper::getShortcode('test_shortcode');

		$this->assertEquals('Shortcode executed', $result);
	}

	/**
	 * @covers ::getShortcode
	 */
	public function testGetShortcodeWithNonExistentShortcode(): void
	{
		$result = $this->wrapper::getShortcode('non_existent_shortcode');

		$this->assertFalse($result);
	}

	/**
	 * @covers ::getShortcode
	 */
	public function testGetShortcodeWithAttributes(): void
	{
		global $shortcode_tags;

		$shortcode_tags['attr_shortcode'] = function ($atts, $content, $tag) {
			$name = $atts['name'] ?? 'default';
			return "Hello {$name}";
		};

		$result = $this->wrapper::getShortcode('attr_shortcode', ['name' => 'World']);

		$this->assertEquals('Hello World', $result);
	}

	/**
	 * @covers ::getShortcode
	 */
	public function testGetShortcodeWithContent(): void
	{
		global $shortcode_tags;

		$shortcode_tags['content_shortcode'] = function ($atts, $content, $tag) {
			return "Content: {$content}";
		};

		$result = $this->wrapper::getShortcode('content_shortcode', [], 'Test content');

		$this->assertEquals('Content: Test content', $result);
	}

	/**
	 * @covers ::getShortcode
	 */
	public function testGetShortcodeWithBothAttributesAndContent(): void
	{
		global $shortcode_tags;

		$shortcode_tags['full_shortcode'] = function ($atts, $content, $tag) {
			$title = $atts['title'] ?? 'No title';
			return "{$title}: {$content}";
		};

		$result = $this->wrapper::getShortcode('full_shortcode', ['title' => 'Message'], 'Hello World');

		$this->assertEquals('Message: Hello World', $result);
	}

	/**
	 * @covers ::getShortcode
	 */
	public function testGetShortcodeWithEmptyAttributes(): void
	{
		global $shortcode_tags;

		$shortcode_tags['empty_attr_shortcode'] = function ($atts, $content, $tag) {
			return \is_array($atts) && empty($atts) ? 'No attributes' : 'Has attributes';
		};

		$result = $this->wrapper::getShortcode('empty_attr_shortcode', []);

		$this->assertEquals('No attributes', $result);
	}

	/**
	 * @covers ::getShortcode
	 */
	public function testGetShortcodeWithEmptyContent(): void
	{
		global $shortcode_tags;

		$shortcode_tags['empty_content_shortcode'] = function ($atts, $content, $tag) {
			return $content === '' ? 'No content' : 'Has content';
		};

		$result = $this->wrapper::getShortcode('empty_content_shortcode', [], '');

		$this->assertEquals('No content', $result);
	}

	/**
	 * @covers ::getShortcode
	 */
	public function testGetShortcodeReceivesCorrectTag(): void
	{
		global $shortcode_tags;

		$shortcode_tags['tag_test'] = function ($atts, $content, $tag) {
			return "Tag is: {$tag}";
		};

		$result = $this->wrapper::getShortcode('tag_test');

		$this->assertEquals('Tag is: tag_test', $result);
	}

	/**
	 * @covers ::getShortcode
	 */
	public function testGetShortcodeWithNumericReturn(): void
	{
		global $shortcode_tags;

		$shortcode_tags['numeric_shortcode'] = function ($atts, $content, $tag) {
			return 42;
		};

		$result = $this->wrapper::getShortcode('numeric_shortcode');

		$this->assertEquals(42, $result);
	}

	/**
	 * @covers ::getShortcode
	 */
	public function testGetShortcodeWithArrayReturn(): void
	{
		global $shortcode_tags;

		$shortcode_tags['array_shortcode'] = function ($atts, $content, $tag) {
			return ['key' => 'value'];
		};

		$result = $this->wrapper::getShortcode('array_shortcode');

		$this->assertEquals(['key' => 'value'], $result);
	}

	/**
	 * @covers ::getShortcode
	 */
	public function testGetShortcodeWithMultipleAttributes(): void
	{
		global $shortcode_tags;

		$shortcode_tags['multi_attr'] = function ($atts, $content, $tag) {
			$name = $atts['name'] ?? '';
			$age = $atts['age'] ?? '';
			$city = $atts['city'] ?? '';
			return "{$name}, {$age}, {$city}";
		};

		$result = $this->wrapper::getShortcode('multi_attr', [
			'name' => 'John',
			'age' => '30',
			'city' => 'NYC'
		]);

		$this->assertEquals('John, 30, NYC', $result);
	}

	/**
	 * @covers ::getShortcode
	 */
	public function testGetShortcodeWithCallableClass(): void
	{
		global $shortcode_tags;

		$callable = new class {
			public function __invoke($atts, $content, $tag) {
				return 'Class callable executed';
			}
		};

		$shortcode_tags['class_shortcode'] = $callable;

		$result = $this->wrapper::getShortcode('class_shortcode');

		$this->assertEquals('Class callable executed', $result);
	}

	/**
	 * @covers ::getShortcode
	 */
	#[DataProvider('shortcodeDataProvider')]
	public function testGetShortcodeWithVariousInputs(string $tag, array $attr, string $content, string $expected): void
	{
		global $shortcode_tags;

		$shortcode_tags[$tag] = function ($atts, $cnt, $tg) {
			$text = $atts['text'] ?? '';
			return $text . $cnt;
		};

		$result = $this->wrapper::getShortcode($tag, $attr, $content);

		$this->assertEquals($expected, $result);
	}

	/**
	 * Data providers
	 */
	public static function shortcodeDataProvider(): array
	{
		return [
			'basic text' => ['test1', ['text' => 'Hello'], '', 'Hello'],
			'text with content' => ['test2', ['text' => 'Hello '], 'World', 'Hello World'],
			'empty attr' => ['test3', [], 'Content', 'Content'],
			'special chars' => ['test4', ['text' => '<b>Bold</b>'], '', '<b>Bold</b>'],
		];
	}
}
