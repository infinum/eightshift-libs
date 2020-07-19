<?php
/**
 * Taxonomy columns abstract class file
 *
 * @package EightshiftLibs\Columns
 */

declare( strict_types=1 );

namespace EightshiftLibs\Columns;

use EightshiftLibs\Core\ServiceInterface;

/**
 * Abstract class AbstractBaseTaxonomyColumns.
 *
 * This abstract class can be extended to add (custom) taxonomy columns in the taxonomy screen.
 */
abstract class AbstractBaseTaxonomyColumns implements ServiceInterface {

  /**
   * Register the taxonomy columns and content in them.
   *
   * @return void
   */
  public function register() : void {
    array_map(
      function( $taxonomy ) {
        add_filter( "manage_edit-{$taxonomy}_columns", [ $this, 'add_column_name' ] );
        add_filter( "manage_{$taxonomy}_custom_column", [ $this, 'render_column_content' ], 10, 3 );
      },
      $this->get_taxonomy_slug()
    );
  }

  /**
   * Add additional taxonomy columns to the columns array.
   *
   * @param array $columns The existing column names array with default taxonomy columns (title, author, date etc.).
   *
   * @return array         Modified column names array.
   */
  abstract public function add_column_name( array $columns ) : array;

  /**
   * Render the taxonomy column content in the custom taxonomy column
   *
   * @param  string $string      Blank string.
   * @param  string $column_name Name of the column.
   * @param  int    $term_id     Term ID.
   *
   * @return string The contetnt to display in the custom column.
   */
  abstract public function render_column_content( string $string, string $column_name, int $term_id ) : string;

  /**
   * Get the slug of the taxonomy where the additional column should appear.
   *
   * @return array The name of the taxonomy.
   */
  abstract protected function get_taxonomy_slug() : array;
}
