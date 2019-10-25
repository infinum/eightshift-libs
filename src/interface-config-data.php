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
 * Interface Config_Data
 *
 * @since 2.0.0
 */
interface Config_Data {

  /**
   * Method returns project name generally used for naming assets handlers, languages and etc.
   *
   * @since 2.0.0
   */
  public static function get_project_name() : string;

  /**
   * Method returns project version generally used for versioning assets handlers.
   *
   * @since 2.0.0
   */
  public static function get_project_version() : string;

  /**
   * Returns project prefix for adding it to all the filters as a prefix because all filters in WordPress live inside a global namespace.
   *
   * @return string Full path to asset.
   *
   * @since 2.0.0 Init
   */
  public static function get_project_prefix() : string;

  /**
   *
   * Returns project env used to define global settings depending on the environment of the project.
   *
   * @return string Project env state.
   *
   * @since 2.0.0 Init
   */
  public static function get_project_env() : string;

  /**
   * Method returns project primary color generally used for styling mobile browser color and splash screens. check head.php for details.
   *
   * @since 2.0.0
   */
  public static function get_project_primary_color() : string;

  /**
   * Return project absolute path for theme use get_template_directory() and for plugin use __DIR__.
   *
   * @param string $path Additional path to add to project path.
   *
   * @return string
   *
   * @since 2.0.0
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
   * @since 2.0.0
   */
  public static function get_config( string $key ) : string;
}
