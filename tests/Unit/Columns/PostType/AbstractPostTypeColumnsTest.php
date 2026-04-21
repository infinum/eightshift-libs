<?php

/**
 * Tests for AbstractPostTypeColumns.
 *
 * @package EightshiftLibs\Tests\Unit\Columns\PostType
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\Columns\PostType;

use Brain\Monkey\Functions;
use EightshiftLibs\Columns\PostType\AbstractPostTypeColumns;
use EightshiftLibs\Services\ServiceInterface;
use EightshiftLibs\Tests\BaseTestCase;
use Mockery;

/**
 * Minimal concrete subclass so the abstract can be instantiated for testing register().
 * The list of post type slugs is injected via constructor so each test controls it.
 */
class ConcretePostTypeColumns extends AbstractPostTypeColumns
{
	/** @var string[] */
	private array $slugs;

	/**
	 * @param string[] $slugs
	 */
	public function __construct(array $slugs = [])
	{
		$this->slugs = $slugs;
	}

	public function addColumnName(array $columns): array
	{
		$columns['custom'] = 'Custom';
		return $columns;
	}

	public function renderColumnContent(string $columnName, int $postId): void
	{
		// Subclass hook — nothing to do for this test double.
	}

	protected function getPostTypeSlugs(): array
	{
		return $this->slugs;
	}
}

/**
 * @coversDefaultClass \EightshiftLibs\Columns\PostType\AbstractPostTypeColumns
 */
class AbstractPostTypeColumnsTest extends BaseTestCase
{
	public function testImplementsServiceInterface(): void
	{
		$this->assertInstanceOf(ServiceInterface::class, new ConcretePostTypeColumns());
	}

	/**
	 * @covers ::register
	 */
	public function testRegisterWithNoPostTypesAddsNoHooks(): void
	{
		Functions\expect('add_filter')->never();
		Functions\expect('add_action')->never();

		(new ConcretePostTypeColumns([]))->register();
	}

	/**
	 * @covers ::register
	 */
	public function testRegisterWithSinglePostTypeAddsScopedHooks(): void
	{
		Functions\expect('add_filter')
			->once()
			->with('manage_post_posts_columns', Mockery::type('array'));

		Functions\expect('add_action')
			->once()
			->with('manage_post_posts_custom_column', Mockery::type('array'), 10, 2);

		(new ConcretePostTypeColumns(['post']))->register();
	}

	/**
	 * @covers ::register
	 */
	public function testRegisterIteratesAllPostTypes(): void
	{
		Functions\expect('add_filter')->twice();
		Functions\expect('add_action')->twice();

		(new ConcretePostTypeColumns(['post', 'page']))->register();
	}
}
