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
 * @since 2.0.0 Added in the project
 */
interface Config_Data {

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
   * Returns the project environment variable descriptor.
   *
   * Used for defining global settings depending on the environment of the project.
   * Can be one of, but not limited to, development, staging, production.
   *
   * @return string Current project environment string.
   *
   * @since 2.0.0 Added in the project
   */
  public static function get_project_env() : string;

  /**
   * Method that returns project REST-API namespace.
   *
   * Used for namespacing projects REST-API routes and fields.
   *
   * @since 2.0.0 Added in the project
   */
  public static function get_project_routes_namespace() : string;

  /**
   * Method that returns project REST-API version.
   *
   * Used for versioning projects REST-API routes and fields.
   *
   * @since 2.0.0 Added in the project
   */
  public static function get_project_routes_version() : string;

  /**
   * Method that returns project primary color.
   *
   * Used for styling the mobile browser color and splash screens. Check head.php for more details.
   *
   * @since 2.0.0 Added in the project
   */
  public static function get_project_primary_color() : string;

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
