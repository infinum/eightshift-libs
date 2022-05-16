<?php

/**
 * File that holds class for WebPMediaColumn registration.
 *
 * @package EightshiftBoilerplate\CustomMeta
 */

declare(strict_types=1);

namespace EightshiftBoilerplate\Columns\Media;

use EightshiftLibs\Columns\Media\AbstractMediaColumns;
use EightshiftLibs\Helpers\Components;

/**
 * Class WebPMediaColumn.
 */
class WebPMediaColumn extends AbstractMediaColumns
{
	/**
	 * Column key const.
	 *
	 * @var string
	 */
	public const COLUMN_KEY = 'webp';

	/**
	 * Add additional media columns to the columns array.
	 *
	 * @param string[] $columns The existing column names array with default media columns (title, author, date etc.).
	 *
	 * @return string[] Modified column names array.
	 */
	public function addColumnName(array $columns): array
	{
		$columns[self::COLUMN_KEY] = \esc_html__('WebP', 'eightshift-libs');
		return $columns;
	}

	/**
	 * Render the media column content in the custom media column
	 *
	 * @param string $columnName Column name.
	 * @param int    $postId ID of the currently-listed media.
	 *
	 * @return string Output based on the column name.
	 */
	public function renderColumnContent(string $columnName, int $postId): string
	{
		if ($columnName === self::COLUMN_KEY) {
			echo Components::existsWebPMedia($postId) ? '<span class="dashicons dashicons-saved"></span>' : '';
		}

		return $columnName;
	}

	/**
	 * Make media columns sortable
	 *
	 * @param string[] $columns Array of columns.
	 *
	 * @return string[] Modified array of columns.
	 */
	public function sortAddedColumns(array $columns): array
	{
		$columns[self::COLUMN_KEY] = \esc_html__('WebP', 'eightshift-libs');

		return $columns;
	}
}
