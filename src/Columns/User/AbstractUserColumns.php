<?php

/**
 * User columns abstract class file
 *
 * @package EightshiftLibs\Columns\User
 */

declare(strict_types=1);

namespace EightshiftLibs\Columns\User;

use EightshiftLibs\Services\ServiceInterface;

/**
 * Abstract class AbstractUserColumns.
 *
 * This abstract class can be extended to add (custom) user columns in the user screen.
 */
abstract class AbstractUserColumns implements ServiceInterface
{

	/**
	 * Register the user columns and content in them.
	 *
	 * @return void
	 */
	public function register(): void
	{
		add_filter('manage_users_columns', [ $this, 'addColumnName' ]);
		add_filter('manage_users_custom_column', [ $this, 'renderColumnContent' ], 10, 3);
		add_filter('manage_users_sortable_columns', [ $this, 'sortAddedColumns' ], 10);
	}

	/**
	 * Add additional user columns to the columns array.
	 *
	 * @param array $columns The existing column names array with default user columns (title, author, date etc.).
	 *
	 * @return array         Modified column names array.
	 */
	abstract public function addColumnName(array $columns ): array;

	/**
	 * Render the user column content in the custom user column
	 *
	 * @param string $output      Custom column output. Default empty.
	 * @param string $columnName Column name.
	 * @param int    $userId     ID of the currently-listed user.
	 *
	 * @return string             Output based on the column name.
	 */
	abstract public function renderColumnContent(string $output, string $columnName, int $userId ): string;

	/**
	 * Make user columns sortable
	 *
	 * @param  array $columns Array of columns.
	 *
	 * @return array          Modified array of columns.
	 */
	abstract public function sortAddedColumns(array $columns ): array;
}
