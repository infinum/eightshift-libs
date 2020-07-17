<?php
/**
 * The file that defines a project config details like prefix, absolute path and etc.
 *
 * @package Eightshiftlibs\Core
 */

declare( strict_types=1 );

namespace Eightshiftlibs\Core;

/**
 * The project config class.
 *
 * @since 2.0.0
 */
abstract class AbstractConfig implements ConfigDataInterface {

  /**
   * Method that returns every string prefixed with project prefix based on project type.
   * It converts all spaces and "_" with "-", also it converts all characters to lowercase.
   *
   * @param string $key String key to append prefix on.
   *
   * @return string Returns key prefixed with project prefix.
   *
   * @since 2.0.0
   */
  public static function get_config( string $key ) : string {
    $project_prefix = static::get_project_prefix();
    $project_prefix = str_replace( ' ', '-', $project_prefix );
    $project_prefix = str_replace( '_', '-', $project_prefix );
    $project_prefix = strtolower( $project_prefix );

    return "{$project_prefix}-{$key}";
  }
}
