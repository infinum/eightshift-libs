<?php
/**
 * Project Config data interface.
 *
 * Used to define the way Config item is retrieved from the Config file.
 *
 * @package Eightshift_Libs\Core
 */

declare( strict_types=1 );

namespace Eightshift_Libs\Core;

/**
 * Interface ConfigDataInterface
 *
 * @since 2.0.0 Added in the project
 */
interface ConfigDataInterface {

  /**
   * Method that returns project name.
   *
   * Generally used for naming assets handlers, languages, etc.
   *
   * @since 2.0.0 Added in the project
   */
  public static function get_project_name() : string;

  /**
   * Method that returns project version.
   *
   * Generally used for versioning asset handlers while enqueueing them.
   *
   * @since 2.0.0 Added in the project
   */
  public static function get_project_version() : string;

  /**
   * Method that returns project prefix.
   *
   * The WordPress filters live in a global namespace, so we need to prefix them to avoid naming collisions.
   *
   * @return string Full path to asset.
   *
   * @since 2.0.0 Added in the project
   */
  public static function get_project_prefix() : string;

  /**
   * Return project absolute path.
   *
   * If used in a theme use get_template_directory() and in case it's used in a plugin use __DIR__.
   *
   * @param string $path Additional path to add to project path.
   *
   * @return string
   *
   * @since 2.0.0 Added in the project
   */
  public static function get_project_path( string $path = '' ) : string;

  /**
   * Method that returns every string prefixed with project prefix based on project type.
   * It converts all spaces and "_" with "-", also it converts all characters to lowercase.
   *
   * @param string $key String key to append prefix on.
   *
   * @return string Returns key prefixed with project prefix.
   *
   * @since 2.0.0 Added in the project
   */
  public static function get_config( string $key ) : string;
}
