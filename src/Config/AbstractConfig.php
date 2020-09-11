<?php
/**
 * The file that defines a project config details like prefix, absolute path and etc.
 *
 * @package EightshiftLibs\Config
 */

declare( strict_types=1 );

namespace EightshiftLibs\Config;

/**
 * The project config class.
 */
abstract class AbstractConfig implements ConfigInterface {

  /**
   * Method that returns every string prefixed with project prefix based on project type.
   * It converts all spaces and "_" with "-", also it converts all characters to lowercase.
   *
   * @param string $key String key to append prefix on.
   *
   * @return string Returns key prefixed with project prefix.
   */
  public static function get_config( string $key ) : string {
    $project_prefix = static::get_project_prefix();
    $project_prefix = str_replace( ' ', '-', $project_prefix );
    $project_prefix = str_replace( '_', '-', $project_prefix );
    $project_prefix = strtolower( $project_prefix );

    return "{$project_prefix}-{$key}";
  }

  /**
   * Return project absolute path.
   *
   * If used in a theme use get_template_directory() and in case it's used in a plugin use __DIR__.
   *
   * @param string $path Additional path to add to project path.
   *
   * @return string
   */
  public static function get_project_path( string $path = '' ) : string {
    $locations = [
      \trailingslashit( \get_stylesheet_directory() ) . $path,
      \trailingslashit( \get_template_directory() ) . $path,
      \trailingslashit( __DIR__ ) . $path,
    ];

    foreach ( $locations as $location ) {
      if ( is_readable( $location ) ) {
        return $location;
      }
    }
  }
}
