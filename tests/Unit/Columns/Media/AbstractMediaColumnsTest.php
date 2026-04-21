<?php

/**
 * Tests for AbstractMediaColumns.
 *
 * @package EightshiftLibs\Tests\Unit\Columns\Media
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\Columns\Media;

use Brain\Monkey\Functions;
use EightshiftLibs\Columns\Media\AbstractMediaColumns;
use EightshiftLibs\Services\ServiceInterface;
use EightshiftLibs\Tests\BaseTestCase;
use Mockery;

/**
 * Minimal concrete subclass so the abstract can be instantiated for testing register().
 */
class ConcreteMediaColumns extends AbstractMediaColumns
{
	public function addColumnName(array $columns): array
	{
		$columns['custom'] = 'Custom';
		return $columns;
	}

	public function renderColumnContent(string $columnName, int $postId): string
	{
		return $columnName === 'custom' ? "media-{$postId}" : '';
	}

	public function sortAddedColumns(array $columns): array
	{
		$columns['custom'] = 'custom';
		return $columns;
	}
}

/**
 * @coversDefaultClass \EightshiftLibs\Columns\Media\AbstractMediaColumns
 */
class AbstractMediaColumnsTest extends BaseTestCase
{
	public function testImplementsServiceInterface(): void
	{
		$this->assertInstanceOf(ServiceInterface::class, new ConcreteMediaColumns());
	}

	/**
	 * @covers ::register
	 */
	public function testRegisterWiresUpAllThreeFilters(): void
	{
		Functions\expect('add_filter')
			->once()
			->with('manage_upload_columns', Mockery::type('array'));

		Functions\expect('add_filter')
			->once()
			->with('manage_media_custom_column', Mockery::type('array'), 10, 2);

		Functions\expect('add_filter')
			->once()
			->with('manage_upload_sortable_columns', Mockery::type('array'), 10);

		(new ConcreteMediaColumns())->register();
	}

	/**
	 * Concrete subclass implementations are not part of the abstract's coverage,
	 * but we verify the abstract's contract: addColumnName returns an array.
	 */
	public function testConcreteAddColumnNameReturnsModifiedArray(): void
	{
		$result = (new ConcreteMediaColumns())->addColumnName(['title' => 'Title']);

		$this->assertArrayHasKey('custom', $result);
		$this->assertArrayHasKey('title', $result);
	}
}
