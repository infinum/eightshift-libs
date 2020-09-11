<?php
/**
 * Post Type columns abstract class file
 *
 * @package EightshiftLibs\Columns\PostType
 */

declare( strict_types=1 );

namespace EightshiftLibs\Columns\PostType;

use EightshiftLibs\Services\ServiceInterface;

/**
 * Abstract class AbstractPostTypeColumns.
 *
 * This abstract class can be extended to add (custom) post columns in the post screen.
 */
abstract class AbstractPostTypeColumns implements ServiceInterface {

  /**
   * Register the post columns and content in them.
   *
   * @return void
   */
  public function register() : void {
    array_map(
      function( $post_type ) {
        add_filter( "manage_{$post_type}_posts_columns", [ $this, 'add_column_name' ] );
        add_action( "manage_{$post_type}_posts_custom_column", [ $this, 'render_column_content' ], 10, 2 );
      },
      $this->get_post_type_slug()
    );
  }

  /**
   * Add additional post columns to the columns array.
   *
   * @param array $columns The existing column names array with default post columns (title, author, date etc.).
   *
   * @return array         Modified column names array.
   */
  abstract public function add_column_name( array $columns ) : array;

  /**
   * Render the post column content in the custom post column
   *
   * @param  string $column_name The name of the column to display.
   * @param  int    $post_id     The current post ID.
   *
   * @return void
   */
  abstract public function render_column_content( string $column_name, int $post_id ) : void;

  /**
   * Get the slug of the post type where the additional column should appear.
   *
   * @return array The name of the post type.
   */
  abstract protected function get_post_type_slug() : array;
}
