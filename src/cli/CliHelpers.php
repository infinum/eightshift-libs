<?php
/**
 * WPCLI Helper methods.
 *
 * @package EightshiftLibs\Cli
 */

namespace EightshiftLibs\Cli;

/**
 * CliHelpers trait
 */
trait CliHelpers {

  /**
   * Generate correct class name from provided string.
   * Remove _, - and empty space. Create a camelcase from string.
   *
   * @param string $file_name File name from string
   *
   * @return string
   */
  public function get_file_name( string $file_name ) : string {
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
  public function get_example_template( string $path ) {
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
  public function output_write( string $output_dir, string $output_file, string $class ) {

    // Set output paths.
    $output_dir = $this->get_output_dir( $output_dir );

    // Set output file path.
    $output_file = $this->get_output_file( $output_file );
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
  public function get_output_dir( $path ) : string {
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
  public function get_output_file( $file ) : string {
    $file = rtrim( $file, '/' );
    $file = trim( $file,'/' );
    return "/{$file}.php";
  }

  /**
   * Replace namespace in class
   *
   * @param array  $args  CLI args array.
   * @param string $class Full class as a string
   *
   * @return string
   *
   * Note: ASCII is used because of composer imposter plugin we are using for prefixing vendors.
   *
   * \x6E\x61\x6D\x65\x73\x70\x61\x63\x65 - Corresponds to "namespace".
   * \x40\x70\x61\x63\x6B\x61\x67\x65 - Corresponds to "@package".
   */
  public function rename_namespace( array $args = [], string $string ) : string {

    $namespace = $this->get_namespace( $args );

    // Namespace.
    $class = preg_replace(
      '/\x40\x70\x61\x63\x6B\x61\x67\x65 (w+|\w+)/',
      "\x40\x70\x61\x63\x6B\x61\x67\x65 {$namespace}",
      $string
    );

    // @package.
    $class = preg_replace(
      '/\x6E\x61\x6D\x65\x73\x70\x61\x63\x65 (w+|\w+\\\\){1,2}/',
      "\x6E\x61\x6D\x65\x73\x70\x61\x63\x65 {$namespace}\\",
      $class
    );

    return $class;
  }

  /**
   * Replace use in class.
   *
   * @param array  $args  CLI args array.
   * @param string $class Full class as a string.
   *
   * @return string
   *
   * Note: ASCII is used because of composer imposter plugin we are using for prefixing vendors.
   *
   * \x75\x73\x65 - Corresponds to "use".
   */
  public function rename_use( array $args = [], string $string ) : string {

    $vendor_prefix = $this->get_vendor_prefix( $args );

    return preg_replace(
      '/\x75\x73\x65 (w+|\w+\\\\)/',
      "\x75\x73\x65 {$vendor_prefix}\\",
      $string
    );
  }

  /**
   * Replace text domain in class.
   *
   * @param array  $args  CLI args array.
   * @param string $class Full class as a string.
   *
   * @return string
   */
  public function rename_text_domain( array $args = [], string $string ) : string {

    $namespace = $this->get_namespace( $args );

    return str_replace(
      'eightshift-libs',
      $namespace,
      $string
    );
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
  public function rename_class_name( string $template_name, string $new_name, string $string ) : string {
    return str_replace( $template_name, $new_name, $string );
  }

  /**
   * Get composer from project or lib.
   *
   * @param array  $args  CLI args array.
   *
   * @return array
   */
  public function get_composer ( array $args = [] ) : array {
    if ( ! isset( $args['config_path'] ) ) {
      if ( function_exists( 'add_action' ) ) {
        $composer_path = dirname( __DIR__, 5 ) . '/composer.json';
      } else {
        $composer_path = dirname( __DIR__, 2 ) . '/composer.json';
      }
    } else {
      $composer_path = $args['config_path'];
    }

    $composer_file = file_get_contents( $composer_path );

    if ( $composer_file === false ) {
      \WP_CLI::error(
        sprintf( 'The composer on "%s" path seems to be missing.', $composer_path )
      );
    }

    return json_decode( $composer_file, true );
  }

  /**
   * Get composers defined namespace.
   *
   * @param array  $args  CLI args array.
   * @return string
   */
  public function get_namespace( array $args = [] ) : string {
    if( isset( $args['namespace'] ) ) {
      $namespace = $args['namespace'];
    }

    if ( empty( $namespace ) ) {
      $composer = $this->get_composer( $args );

      $namespace = rtrim( array_key_first($composer['autoload']['psr-4']), '\\' );
    }

    return $namespace;
  }

  /**
   * Get composers defined vendor prefix.
   *
   * @param array  $args  CLI args array.
   * @return string
   */
  public function get_vendor_prefix( array $args = [] ) : string {
    if( isset( $args['vendor_prefix'] ) ) {
      $vendor_prefix = $args['vendor_prefix'];
    }

    if ( empty( $vendor_prefix ) ) {
      $composer = $this->get_composer( $args );

      $vendor_prefix = $composer['extra']['imposter']['namespace'] ?? 'EightshiftLibs';
    }

    return $vendor_prefix;
  }
}
