<?php

namespace Tests\Unit\Columns;

use EightshiftLibs\Columns\Taxonomy\AbstractTaxonomyColumns;

test('Hooks are registered for the custom taxonomy columns', function() {
	class TaxonomyColumnMock extends AbstractTaxonomyColumns {
		public const TAXONOMY = 'example';

		public function addColumnName(array $columns): array
		{
			return [];
		}

		public function renderColumnContent(string $_string, string $columnName, int $termId): string
		{
			return 'content';
		}

		protected function getTaxonomySlug(): array
		{
			return [self::TAXONOMY];
		}
	};

	$mockTaxonomyColumn = new TaxonomyColumnMock();

	$mockTaxonomyColumn->register();
	$taxonomy = $mockTaxonomyColumn::TAXONOMY;

	$this->assertNotFalse(has_filter("manage_edit-{$taxonomy}_columns", 'Tests\Unit\Columns\TaxonomyColumnMock->addColumnName()'), 'Manage edit filter wasn\'t registered');
	$this->assertNotFalse(has_filter("manage_{$taxonomy}_custom_column", 'Tests\Unit\Columns\TaxonomyColumnMock->renderColumnContent()'), 'Manage filter wasn\'t registered');
	$this->assertSame(10, has_filter("manage_edit-{$taxonomy}_columns", 'Tests\Unit\Columns\TaxonomyColumnMock->addColumnName()'), 'Manage edit filter priority was not correct');
	$this->assertSame(10, has_filter("manage_{$taxonomy}_custom_column", 'Tests\Unit\Columns\TaxonomyColumnMock->renderColumnContent()'), 'Manage filter priority was not correct');
});
