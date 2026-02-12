<?php

/**
 * Tests for AbstractPostType class
 *
 * @package EightshiftLibs\Tests\Unit\CustomPostType
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\CustomPostType;

use Brain\Monkey;
use Brain\Monkey\Functions;
use EightshiftLibs\CustomPostType\AbstractPostType;
use EightshiftLibs\Services\ServiceInterface;
use EightshiftLibs\Services\ServiceCliInterface;
use EightshiftLibs\Tests\BaseTestCase;

/**
 * AbstractPostTypeTest class
 */
class AbstractPostTypeTest extends BaseTestCase
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
	 * Test that AbstractPostType implements ServiceInterface
	 *
	 * @return void
	 */
	public function testImplementsServiceInterface(): void
	{
		$postType = new ConcretePostType();

		$this->assertInstanceOf(ServiceInterface::class, $postType);
	}

	/**
	 * Test that AbstractPostType implements ServiceCliInterface
	 *
	 * @return void
	 */
	public function testImplementsServiceCliInterface(): void
	{
		$postType = new ConcretePostType();

		$this->assertInstanceOf(ServiceCliInterface::class, $postType);
	}

	/**
	 * Test that register method is callable
	 *
	 * @return void
	 */
	public function testRegisterIsCallable(): void
	{
		$postType = new ConcretePostType();

		$this->assertTrue(\is_callable([$postType, 'register']));
	}

	/**
	 * Test that postTypeRegisterCallback method is callable
	 *
	 * @return void
	 */
	public function testPostTypeRegisterCallbackIsCallable(): void
	{
		$postType = new ConcretePostType();

		$this->assertTrue(\is_callable([$postType, 'postTypeRegisterCallback']));
	}

	/**
	 * Test that getPostTypeSlug returns expected value
	 *
	 * @return void
	 */
	public function testGetPostTypeSlugReturnsExpectedValue(): void
	{
		$postType = new ConcretePostType();

		$reflection = new \ReflectionMethod($postType, 'getPostTypeSlug');

		$this->assertEquals('test_post_type', $reflection->invoke($postType));
	}

	/**
	 * Test that getPostTypeArguments returns expected array
	 *
	 * @return void
	 */
	public function testGetPostTypeArgumentsReturnsExpectedArray(): void
	{
		$postType = new ConcretePostType();

		$reflection = new \ReflectionMethod($postType, 'getPostTypeArguments');
		$result = $reflection->invoke($postType);

		$this->assertIsArray($result);
		$this->assertArrayHasKey('label', $result);
		$this->assertArrayHasKey('public', $result);
		$this->assertEquals('Test Post Type', $result['label']);
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

		$postType = new ConcretePostType();
		$postType->register();
	}

	/**
	 * Test that postTypeRegisterCallback calls register_post_type
	 *
	 * @return void
	 */
	public function testPostTypeRegisterCallbackRegistersPostType(): void
	{
		Functions\expect('register_post_type')
			->once()
			->with('test_post_type', ['label' => 'Test Post Type', 'public' => true]);

		$postType = new ConcretePostType();
		$postType->postTypeRegisterCallback();
	}
}

/**
 * Concrete implementation of AbstractPostType for testing
 */
class ConcretePostType extends AbstractPostType
{
	/**
	 * Get the slug to use for the custom post type
	 *
	 * @return string
	 */
	protected function getPostTypeSlug(): string
	{
		return 'test_post_type';
	}

	/**
	 * Get the arguments that configure the custom post type
	 *
	 * @return array<string, mixed>
	 */
	protected function getPostTypeArguments(): array
	{
		return [
			'label' => 'Test Post Type',
			'public' => true,
		];
	}
}
