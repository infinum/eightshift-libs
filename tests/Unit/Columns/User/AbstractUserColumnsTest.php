<?php

/**
 * Tests for AbstractUserColumns.
 *
 * @package EightshiftLibs\Tests\Unit\Columns\User
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\Columns\User;

use Brain\Monkey\Functions;
use EightshiftLibs\Columns\User\AbstractUserColumns;
use EightshiftLibs\Services\ServiceInterface;
use EightshiftLibs\Tests\BaseTestCase;
use Mockery;

/**
 * Minimal concrete subclass so the abstract can be instantiated for testing register().
 */
class ConcreteUserColumns extends AbstractUserColumns
{
	public function addColumnName(array $columns): array
	{
		$columns['custom'] = 'Custom';
		return $columns;
	}

	public function renderColumnContent(string $output, string $columnName, int $userId): string
	{
		return $columnName === 'custom' ? "user-{$userId}" : $output;
	}

	public function sortAddedColumns(array $columns): array
	{
		$columns['custom'] = 'custom';
		return $columns;
	}
}

/**
 * @coversDefaultClass \EightshiftLibs\Columns\User\AbstractUserColumns
 */
class AbstractUserColumnsTest extends BaseTestCase
{
	public function testImplementsServiceInterface(): void
	{
		$this->assertInstanceOf(ServiceInterface::class, new ConcreteUserColumns());
	}

	/**
	 * @covers ::register
	 */
	public function testRegisterWiresUpAllThreeFilters(): void
	{
		Functions\expect('add_filter')
			->once()
			->with('manage_users_columns', Mockery::type('array'));

		Functions\expect('add_filter')
			->once()
			->with('manage_users_custom_column', Mockery::type('array'), 10, 3);

		Functions\expect('add_filter')
			->once()
			->with('manage_users_sortable_columns', Mockery::type('array'), 10);

		(new ConcreteUserColumns())->register();
	}
}
