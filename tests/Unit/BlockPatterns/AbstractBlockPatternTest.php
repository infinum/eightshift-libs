<?php

/**
 * Tests for AbstractBlockPattern class
 *
 * @package EightshiftLibs\Tests\Unit\BlockPatterns
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\BlockPatterns;

use Brain\Monkey;
use Brain\Monkey\Functions;
use EightshiftLibs\BlockPatterns\AbstractBlockPattern;
use EightshiftLibs\Services\ServiceInterface;
use EightshiftLibs\Tests\BaseTestCase;

/**
 * AbstractBlockPatternTest class
 */
class AbstractBlockPatternTest extends BaseTestCase
{
	/**
	 * Set up before each test
	 *
	 * @return void
	 */
	protected function setUp(): void
	{
		parent::setUp();
		Monkey\setUp();
	}

	/**
	 * Tear down after each test
	 *
	 * @return void
	 */
	protected function tearDown(): void
	{
		Monkey\tearDown();
		parent::tearDown();
	}

	/**
	 * Test that AbstractBlockPattern implements ServiceInterface
	 *
	 * @return void
	 */
	public function testImplementsServiceInterface(): void
	{
		$pattern = new ConcreteBlockPattern();

		$this->assertInstanceOf(ServiceInterface::class, $pattern);
	}

	/**
	 * Test that register method is callable
	 *
	 * @return void
	 */
	public function testRegisterIsCallable(): void
	{
		$pattern = new ConcreteBlockPattern();

		$this->assertTrue(\is_callable([$pattern, 'register']));
	}

	/**
	 * Test that registerBlockPattern method is callable
	 *
	 * @return void
	 */
	public function testRegisterBlockPatternIsCallable(): void
	{
		$pattern = new ConcreteBlockPattern();

		$this->assertTrue(\is_callable([$pattern, 'registerBlockPattern']));
	}

	/**
	 * Test that register method adds action hook
	 *
	 * @return void
	 */
	public function testRegisterAddsInitAction(): void
	{
		Functions\expect('add_action')
			->once()
			->with('init', \Mockery::type('array'));

		$pattern = new ConcreteBlockPattern();
		$pattern->register();
	}

	/**
	 * Test that registerBlockPattern calls register_block_pattern with correct arguments
	 *
	 * @return void
	 */
	public function testRegisterBlockPatternCallsRegisterBlockPattern(): void
	{
		Functions\expect('register_block_pattern')
			->once()
			->with(
				'test/pattern-name',
				[
					'title' => 'Test Pattern',
					'description' => 'A test block pattern',
					'content' => '<!-- wp:paragraph --><p>Test content</p><!-- /wp:paragraph -->',
					'categories' => [],
					'keywords' => [],
				]
			);

		$pattern = new ConcreteBlockPattern();
		$pattern->registerBlockPattern();
	}

	/**
	 * Test that getCategories returns empty array by default
	 *
	 * @return void
	 */
	public function testGetCategoriesReturnsEmptyArrayByDefault(): void
	{
		$pattern = new ConcreteBlockPattern();

		$reflection = new \ReflectionMethod($pattern, 'getCategories');

		$this->assertEquals([], $reflection->invoke($pattern));
	}

	/**
	 * Test that getKeywords returns empty array by default
	 *
	 * @return void
	 */
	public function testGetKeywordsReturnsEmptyArrayByDefault(): void
	{
		$pattern = new ConcreteBlockPattern();

		$reflection = new \ReflectionMethod($pattern, 'getKeywords');

		$this->assertEquals([], $reflection->invoke($pattern));
	}

	/**
	 * Test that getName returns expected value
	 *
	 * @return void
	 */
	public function testGetNameReturnsExpectedValue(): void
	{
		$pattern = new ConcreteBlockPattern();

		$reflection = new \ReflectionMethod($pattern, 'getName');

		$this->assertEquals('test/pattern-name', $reflection->invoke($pattern));
	}

	/**
	 * Test that getTitle returns expected value
	 *
	 * @return void
	 */
	public function testGetTitleReturnsExpectedValue(): void
	{
		$pattern = new ConcreteBlockPattern();

		$reflection = new \ReflectionMethod($pattern, 'getTitle');

		$this->assertEquals('Test Pattern', $reflection->invoke($pattern));
	}

	/**
	 * Test that getDescription returns expected value
	 *
	 * @return void
	 */
	public function testGetDescriptionReturnsExpectedValue(): void
	{
		$pattern = new ConcreteBlockPattern();

		$reflection = new \ReflectionMethod($pattern, 'getDescription');

		$this->assertEquals('A test block pattern', $reflection->invoke($pattern));
	}

	/**
	 * Test that getContent returns expected value
	 *
	 * @return void
	 */
	public function testGetContentReturnsExpectedValue(): void
	{
		$pattern = new ConcreteBlockPattern();

		$reflection = new \ReflectionMethod($pattern, 'getContent');

		$this->assertEquals('<!-- wp:paragraph --><p>Test content</p><!-- /wp:paragraph -->', $reflection->invoke($pattern));
	}
}

/**
 * Concrete implementation of AbstractBlockPattern for testing
 */
class ConcreteBlockPattern extends AbstractBlockPattern
{
	/**
	 * Get the pattern name
	 *
	 * @return string
	 */
	protected function getName(): string
	{
		return 'test/pattern-name';
	}

	/**
	 * Get the pattern title
	 *
	 * @return string
	 */
	protected function getTitle(): string
	{
		return 'Test Pattern';
	}

	/**
	 * Get the pattern description
	 *
	 * @return string
	 */
	protected function getDescription(): string
	{
		return 'A test block pattern';
	}

	/**
	 * Get the pattern content
	 *
	 * @return string
	 */
	protected function getContent(): string
	{
		return '<!-- wp:paragraph --><p>Test content</p><!-- /wp:paragraph -->';
	}
}
