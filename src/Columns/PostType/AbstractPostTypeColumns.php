<?php

/**
 * Post Type columns abstract class file
 *
 * @package EightshiftLibs\Columns\PostType
 */

declare(strict_types=1);

namespace EightshiftLibs\Columns\PostType;

use EightshiftLibs\Services\ServiceInterface;

/**
 * Abstract class AbstractPostTypeColumns.
 *
 * This abstract class can be extended to add (custom) post columns in the post screen.
 */
abstract class AbstractPostTypeColumns implements ServiceInterface
{

	/**
	 * Register the post columns and content in them.
	 *
	 * @return void
	 */
	public function register(): void
	{
		$postTypes = $this->getPostTypeSlug();

		foreach ($postTypes as $postType) {
			\add_filter("manage_{$postType}_posts_columns", [$this, 'addColumnName']);
			\add_action("manage_{$postType}_posts_custom_column", [$this, 'renderColumnContent'], 10, 2);
		}
	}

	/**
	 * Add additional post columns to the columns array.
	 *
	 * @param array $columns The existing column names array with default post columns (title, author, date etc.).
	 *
	 * @return array Modified column names array.
	 */
	abstract public function addColumnName(array $columns): array;

	/**
	 * Render the post column content in the custom post column
	 *
	 * @param string $columnName The name of the column to display.
	 * @param int    $postId The current post ID.
	 *
	 * @return void
	 */
	abstract public function renderColumnContent(string $columnName, int $postId): void;

	/**
	 * Get the slug of the post type where the additional column should appear.
	 *
	 * @return array The name of the post type.
	 */
	abstract protected function getPostTypeSlug(): array;
}
