<?php
/**
 * WPCLI Helper methods.
 *
 * @package EightshiftLibs\Cli
 */

namespace EightshiftLibs\Cli;

/**
 * CliHelpers class
 */
class CliHelpers {

  /**
   * Generate correct class name from provided string.
   * Remove _, - and empty space. Create a camelcase from string.
   *
   * @param string $file_name File name from string
   *
   * @return string
   */
  public static function get_class_name( string $file_name ) : string {
    $class = explode( '_', str_replace( '-', '_', str_replace( ' ', '_', strtolower( $file_name ) ) ) );

    $class_name = array_map(
      function( $item ) {
        return ucfirst( $item );
      },
      $class
    );

    return implode( '', $class_name );
  }

  /**
   * Get template file content and throw error if template is missing.
   *
   * @param string $path Absolute path to file.
   *
   * @return string|Error
   */
  public static function get_template( string $path ) {
    // Read the template contents, and replace the placeholders with provided variables.
    $template_file = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions

    if ( $template_file === false ) {
      \WP_CLI::error(
        sprintf( 'The template "%s" seems to be missing.', $path )
      );
    }

    return $template_file;
  }

  /**
   * Open an updated file and create it on output location.
   *
   * @param string $output_dir  Absolute path to output from project root dir.
   * @param string $output_file Absolute path to output file.
   * @param string $class       Modified class.
   *
   * @return Error|Success
   */
  public static function output_write( string $output_dir, string $output_file, string $class ) {

    // Set output paths.
    $output_dir = CliHelpers::get_output_dir( $output_dir );

    // Set output file path.
    $output_file = CliHelpers::get_output_file( $output_file );
    $output_file = "{$output_dir}{$output_file}";

    // Bailout if file already exists.
    if ( file_exists( $output_file ) ) {
      \WP_CLI::error(
        sprintf( 'The file "%s" can\'t be generated because it already exists.', $output_file )
      );
    }

    // Create output dir if it doesn't exist.
    if ( ! is_dir( $output_dir ) ) {
      mkdir( $output_dir, 0755, true );
    }

    // Open a new file on output.
    $fp = fopen( $output_file, 'wb' ); // phpcs:ignore WordPress.WP.AlternativeFunctions

    // If there is any error bailout. For example, user permission.
    if ( ! $fp ) {
      \WP_CLI::error(
        sprintf( "File %s couldn't be created. There was an error.", $output_file )
      );
    }

    // Write and close.
    fwrite( $fp, $class ); // phpcs:ignore WordPress.WP.AlternativeFunctions
    fclose( $fp ); // phpcs:ignore WordPress.WP.AlternativeFunctions

    // Return success.
    \WP_CLI::success(
      sprintf( "File %s successfully created.", $output_file )
    );
  }

  /**
   * Get full output dir path.
   *
   * @param string $path Project specific path.
   *
   * @return string
   */
  public static function get_output_dir( $path ) : string {
    if ( function_exists( 'add_action' ) ) {
      $root = dirname( __DIR__, 5 );
    } else {
      $root = dirname( __DIR__, 2 ) . '/cli-output';
    }

    $root = rtrim( $root, '/' );
    $root = trim( $root,'/' );

    $path = rtrim( $path, '/' );
    $path = trim( $path,'/' );

    return "/{$root}/{$path}";
  }

  /**
   * Get full output dir path.
   *
   * @param string $root Parent root path.
   * @param string $path Project specific path.
   *
   * @return string
   */
  public static function get_output_file( $file ) : string {
    $file = rtrim( $file, '/' );
    $file = trim( $file,'/' );
    return "/{$file}.php";
  }

  /**
   * Replace namespace in class
   *
   * @param string $namespace Class nammespace.
   * @param string $class     Full class as a string
   *
   * @return string
   */
  public static function change_namespace( string $namespace, string $string ) : string {
    $class = preg_replace( '/@package (w+|\w+)/', "@package {$namespace}", $string );
    $class = preg_replace( '/namespace (w+|\w+\\\\){1,2}/', "namespace {$namespace}\\", $class );

    return $class;
  }

  /**
   * Replace use in class.
   *
   * @param string $vendor_prefix Class vendor prefix.
   * @param string $class         Full class as a string.
   *
   * @return string
   */
  public static function change_use( string $vendor_prefix, string $string ) : string {
    if ( ! function_exists( 'add_action' ) ) {
      $vendor_prefix = 'EightshiftLibs';
    }

    return preg_replace( '/use (w+|\w+\\\\)/', "use {$vendor_prefix}\\", $string );
  }

  /**
   * Change Class full name.
   *
   * @param string $template_name Current template.
   * @param string $new_name      New Class Name.
   * @param string $class         Full class as a string.
   *
   * @return string
   */
  public static function change_class_name( string $template_name, string $new_name, string $string ) : string {
    return str_replace( $template_name, $new_name, $string );
  }
}
