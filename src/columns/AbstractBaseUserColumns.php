<?php
/**
 * User columns abstract class file
 *
 * @package EightshiftLibs\Columns
 */

declare( strict_types=1 );

namespace EightshiftLibs\Columns;

use EightshiftLibs\Core\ServiceInterface;

/**
 * Abstract class AbstractBaseUserColumns.
 *
 * This abstract class can be extended to add (custom) user columns in the user screen.
 */
abstract class AbstractBaseUserColumns implements ServiceInterface {

  /**
   * Register the user columns and content in them.
   *
   * @return void
   */
  public function register() : void {
    add_filter( 'manage_users_columns', [ $this, 'add_column_name' ] );
    add_filter( 'manage_users_custom_column', [ $this, 'render_column_content' ], 10, 3 );
    add_filter( 'manage_users_sortable_columns', [ $this, 'sort_added_columns' ], 10 );
  }

  /**
   * Add additional user columns to the columns array.
   *
   * @param array $columns The existing column names array with default user columns (title, author, date etc.).
   *
   * @return array         Modified column names array.
   */
  abstract public function add_column_name( array $columns ) : array;

  /**
   * Render the user column content in the custom user column
   *
   * @param string $output      Custom column output. Default empty.
   * @param string $column_name Column name.
   * @param int    $user_id     ID of the currently-listed user.
   *
   * @return string             Output based on the column name.
   */
  abstract public function render_column_content( string $output, string $column_name, int $user_id ) : string;

  /**
   * Make user columns sortable
   *
   * @param  array $columns Array of columns.
   *
   * @return array          Modified array of columns.
   */
  abstract public function sort_added_columns( array $columns ) : array;
}
