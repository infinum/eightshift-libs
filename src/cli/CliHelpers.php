<?php
/**
 * WPCLI Helper methods.
 *
 * @package EightshiftLibs\Cli
 */

declare( strict_types=1 );

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
    $class    = explode( '_', str_replace( '-', '_', str_replace( ' ', '_', strtolower( $file_name ) ) ) );

    $class_name = implode( '_', array_map( function( $item ) { // phpcs:ignore PEAR.Functions.FunctionCallSignature
        return ucfirst( $item );
    }, $class ) ); // phpcs:ignore PEAR.Functions.FunctionCallSignature

    return $class_name;
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
   * @param string $root        Absolute path to project root.
   * @param string $output_dir  Absolute path to output from project root dir.
   * @param string $output_file Absolute path to output file.
   * @param string $class       Modified class.
   *
   * @return Error|Success
   */
  public static function output_write( string $root, string $output_dir, string $output_file, string $class ) {

    // Set output paths.
    $output_dir = CliHelpers::get_output_dir( $root, $output_dir );

    // Set output file path.
    $output_file = CliHelpers::get_output_file( $output_file );
    $output_file = "{$output_dir}{$output_file}";

    if ( file_exists( $output_file ) ) {
      \WP_CLI::error(
        sprintf( 'The file "%s" can\'t be generated because it already exists.', $output_file )
      );
    }

    if ( ! is_dir( $output_dir ) ) {
      mkdir( $output_dir, 0755, true );
    }

    $fp = fopen( $output_file, 'wb' ); // phpcs:ignore WordPress.WP.AlternativeFunctions

    if ( ! $fp ) {
      \WP_CLI::error(
        sprintf( "File %s couldn't be created. There was an error.", $output_file )
      );

    }

    fwrite( $fp, $class ); // phpcs:ignore WordPress.WP.AlternativeFunctions
    fclose( $fp ); // phpcs:ignore WordPress.WP.AlternativeFunctions

    \WP_CLI::success(
      sprintf( "File %s successfully created.", $output_file )
    );
  }

  /**
   * Get full output dir path.
   *
   * @param string $root Parent root path.
   * @param string $path Project specific path.
   *
   * @return string
   */
  public static function get_output_dir( $root, $path ) : string {
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
}
