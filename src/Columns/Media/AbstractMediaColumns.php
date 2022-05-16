<?php

/**
 * Media columns abstract class file
 *
 * @package EightshiftLibs\Columns\Media
 */

declare(strict_types=1);

namespace EightshiftLibs\Columns\Media;

use EightshiftLibs\Services\ServiceInterface;

/**
 * Abstract class AbstractMediaColumns.
 *
 * This abstract class can be extended to add (custom) media columns in the user screen.
 */
abstract class AbstractMediaColumns implements ServiceInterface
{
	/**
	 * Register the media columns and content in them.
	 *
	 * @return void
	 */
	public function register(): void
	{
		\add_filter('manage_media_columns', [$this, 'addColumnName']);
		\add_filter('manage_media_custom_column', [$this, 'renderColumnContent'], 10, 2);
		\add_filter('manage_media_sortable_columns', [$this, 'sortAddedColumns'], 10);
	}

	/**
	 * Add additional media columns to the columns array.
	 *
	 * @param string[] $columns The existing column names array with default media columns (title, author, date etc.).
	 *
	 * @return string[] Modified column names array.
	 */
	abstract public function addColumnName(array $columns): array;

	/**
	 * Render the media column content in the custom media column
	 *
	 * @param string $columnName Column name.
	 * @param int $postId ID of the currently-listed media.
	 *
	 * @return string Output based on the column name.
	 */
	abstract public function renderColumnContent(string $columnName, int $postId): string;

	/**
	 * Make media columns sortable
	 *
	 * @param string[] $columns Array of columns.
	 *
	 * @return string[] Modified array of columns.
	 */
	abstract public function sortAddedColumns(array $columns): array;
}
