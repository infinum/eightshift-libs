<?php

/**
 * Tests for AbstractTaxonomyColumns.
 *
 * @package EightshiftLibs\Tests\Unit\Columns\Taxonomy
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\Columns\Taxonomy;

use Brain\Monkey\Functions;
use EightshiftLibs\Columns\Taxonomy\AbstractTaxonomyColumns;
use EightshiftLibs\Services\ServiceInterface;
use EightshiftLibs\Tests\BaseTestCase;
use Mockery;

/**
 * Minimal concrete subclass so the abstract can be instantiated for testing register().
 * The list of taxonomy slugs is injected via constructor so each test controls it.
 */
class ConcreteTaxonomyColumns extends AbstractTaxonomyColumns
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

	public function renderColumnContent(string $columnOutput, string $columnName, int $termId): string
	{
		return $columnOutput;
	}

	protected function getTaxonomySlug(): array
	{
		return $this->slugs;
	}
}

/**
 * @coversDefaultClass \EightshiftLibs\Columns\Taxonomy\AbstractTaxonomyColumns
 */
class AbstractTaxonomyColumnsTest extends BaseTestCase
{
	public function testImplementsServiceInterface(): void
	{
		$this->assertInstanceOf(ServiceInterface::class, new ConcreteTaxonomyColumns());
	}

	/**
	 * @covers ::register
	 */
	public function testRegisterWithNoTaxonomiesAddsNoHooks(): void
	{
		Functions\expect('add_filter')->never();

		(new ConcreteTaxonomyColumns([]))->register();
	}

	/**
	 * @covers ::register
	 */
	public function testRegisterWithSingleTaxonomyAddsScopedHooks(): void
	{
		Functions\expect('add_filter')
			->once()
			->with('manage_edit-category_columns', Mockery::type('array'));

		Functions\expect('add_filter')
			->once()
			->with('manage_category_custom_column', Mockery::type('array'), 10, 3);

		(new ConcreteTaxonomyColumns(['category']))->register();
	}

	/**
	 * @covers ::register
	 */
	public function testRegisterIteratesAllTaxonomies(): void
	{
		// 2 filters per taxonomy × 2 taxonomies = 4 add_filter calls.
		Functions\expect('add_filter')->times(4);

		(new ConcreteTaxonomyColumns(['category', 'post_tag']))->register();
	}
}
