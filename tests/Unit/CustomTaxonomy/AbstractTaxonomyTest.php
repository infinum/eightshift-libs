<?php

/**
 * Tests for AbstractTaxonomy class
 *
 * @package EightshiftLibs\Tests\Unit\CustomTaxonomy
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\CustomTaxonomy;

use Brain\Monkey;
use Brain\Monkey\Functions;
use EightshiftLibs\CustomTaxonomy\AbstractTaxonomy;
use EightshiftLibs\Services\ServiceInterface;
use EightshiftLibs\Services\ServiceCliInterface;
use EightshiftLibs\Tests\BaseTestCase;

/**
 * AbstractTaxonomyTest class
 */
class AbstractTaxonomyTest extends BaseTestCase
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
	 * Test that AbstractTaxonomy implements ServiceInterface
	 *
	 * @return void
	 */
	public function testImplementsServiceInterface(): void
	{
		$taxonomy = new ConcreteTaxonomy();

		$this->assertInstanceOf(ServiceInterface::class, $taxonomy);
	}

	/**
	 * Test that AbstractTaxonomy implements ServiceCliInterface
	 *
	 * @return void
	 */
	public function testImplementsServiceCliInterface(): void
	{
		$taxonomy = new ConcreteTaxonomy();

		$this->assertInstanceOf(ServiceCliInterface::class, $taxonomy);
	}

	/**
	 * Test that register method is callable
	 *
	 * @return void
	 */
	public function testRegisterIsCallable(): void
	{
		$taxonomy = new ConcreteTaxonomy();

		$this->assertTrue(\is_callable([$taxonomy, 'register']));
	}

	/**
	 * Test that taxonomyRegisterCallback method is callable
	 *
	 * @return void
	 */
	public function testTaxonomyRegisterCallbackIsCallable(): void
	{
		$taxonomy = new ConcreteTaxonomy();

		$this->assertTrue(\is_callable([$taxonomy, 'taxonomyRegisterCallback']));
	}

	/**
	 * Test that getTaxonomySlug returns expected value
	 *
	 * @return void
	 */
	public function testGetTaxonomySlugReturnsExpectedValue(): void
	{
		$taxonomy = new ConcreteTaxonomy();

		$reflection = new \ReflectionMethod($taxonomy, 'getTaxonomySlug');

		$this->assertEquals('test_taxonomy', $reflection->invoke($taxonomy));
	}

	/**
	 * Test that getPostTypeSlug returns expected value
	 *
	 * @return void
	 */
	public function testGetPostTypeSlugReturnsExpectedValue(): void
	{
		$taxonomy = new ConcreteTaxonomy();

		$reflection = new \ReflectionMethod($taxonomy, 'getPostTypeSlug');

		$this->assertEquals('post', $reflection->invoke($taxonomy));
	}

	/**
	 * Test that getTaxonomyArguments returns expected array
	 *
	 * @return void
	 */
	public function testGetTaxonomyArgumentsReturnsExpectedArray(): void
	{
		$taxonomy = new ConcreteTaxonomy();

		$reflection = new \ReflectionMethod($taxonomy, 'getTaxonomyArguments');
		$result = $reflection->invoke($taxonomy);

		$this->assertIsArray($result);
		$this->assertArrayHasKey('label', $result);
		$this->assertEquals('Test Taxonomy', $result['label']);
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

		$taxonomy = new ConcreteTaxonomy();
		$taxonomy->register();
	}

	/**
	 * Test that taxonomyRegisterCallback calls register_taxonomy
	 *
	 * @return void
	 */
	public function testTaxonomyRegisterCallbackRegistersTaxonomy(): void
	{
		Functions\expect('register_taxonomy')
			->once()
			->with('test_taxonomy', 'post', ['label' => 'Test Taxonomy', 'public' => true]);

		$taxonomy = new ConcreteTaxonomy();
		$taxonomy->taxonomyRegisterCallback();
	}
}

/**
 * Concrete implementation of AbstractTaxonomy for testing
 */
class ConcreteTaxonomy extends AbstractTaxonomy
{
	/**
	 * Get the slug of the custom taxonomy
	 *
	 * @return string
	 */
	protected function getTaxonomySlug(): string
	{
		return 'test_taxonomy';
	}

	/**
	 * Get the post type slug(s) that use the taxonomy
	 *
	 * @return string|string[]
	 */
	protected function getPostTypeSlug()
	{
		return 'post';
	}

	/**
	 * Get the arguments that configure the custom taxonomy
	 *
	 * @return array<string, mixed>
	 */
	protected function getTaxonomyArguments(): array
	{
		return [
			'label' => 'Test Taxonomy',
			'public' => true,
		];
	}
}
