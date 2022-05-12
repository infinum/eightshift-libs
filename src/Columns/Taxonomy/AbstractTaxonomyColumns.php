<?php

/**
 * Taxonomy columns abstract class file
 *
 * @package EightshiftLibs\Columns\Taxonomy
 */

declare(strict_types=1);

namespace EightshiftLibs\Columns\Taxonomy;

use EightshiftLibs\Services\ServiceInterface;

/**
 * Abstract class AbstractTaxonomyColumns.
 *
 * This abstract class can be extended to add (custom) taxonomy columns in the taxonomy screen.
 */
abstract class AbstractTaxonomyColumns implements ServiceInterface
{
	/**
	 * Register the taxonomy columns and content in them.
	 *
	 * @return void
	 */
	public function register(): void
	{
		$taxonomies = $this->getTaxonomySlug();

		foreach ($taxonomies as $taxonomy) {
			\add_filter("manage_edit-{$taxonomy}_columns", [$this, 'addColumnName']);
			\add_filter("manage_{$taxonomy}_custom_column", [$this, 'renderColumnContent'], 10, 3);
		}
	}

	/**
	 * Add additional taxonomy columns to the columns array.
	 *
	 * @param string[] $columns The existing column names array with default taxonomy columns (title, author, date etc.).
	 *
	 * @return string[] Modified column names array.
	 */
	abstract public function addColumnName(array $columns): array;

	/**
	 * Render the taxonomy column content in the custom taxonomy column
	 *
	 * @param string $columnName Name of the column.
	 * @param int    $termId Term ID.
	 *
	 * @return string The content to display in the custom column.
	 */
	abstract public function renderColumnContent(string $columnName, int $termId): string;

	/**
	 * Get the array of slugs of the taxonomies where the additional column should appear.
	 *
	 * @return string[] Array containing the names of the taxonomies.
	 */
	abstract protected function getTaxonomySlug(): array;
}
