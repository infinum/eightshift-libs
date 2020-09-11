<?php
/**
 * WPCLI Helper methods.
 *
 * @package EightshiftLibs\Cli
 */

declare( strict_types=1 );

namespace EightshiftLibs\Cli;

/**
 * CliHelpers trait
 */
trait CliHelpers {

  /**
   * Generate correct class name from provided string.
   * Remove _, - and empty space. Create a camelcase from string.
   *
   * @param string $file_name File name from string.
   *
   * @return string
   */
  public function get_file_name( string $file_name ) : string {

    if ( strpos( $file_name, ' ' ) !== false ) {
      $file_name = strtolower( $file_name );
    }

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
   * @param string $current_dir Absolute path to dir where example is.
   * @param string $file_name   File Name of example.
   *
   * @return string|Error
   */
  public function get_example_template( string $current_dir, string $file_name ) : string {

    // If you pass file name with extension the version will be used.
    if ( strpos( $file_name, '.' ) !== false ) {
      $path = "{$current_dir}/{$file_name}";
    } else {
      $path = "{$current_dir}/{$this->get_example_file_name( $file_name )}.php";
    }

    // Read the template contents, and replace the placeholders with provided variables.
    $template_file = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions

    if ( $template_file === false ) {
      \WP_CLI::error( "The template {$path} seems to be missing." );
    }

    return $template_file;
  }

  /**
   * Generate example template file/class name.
   *
   * @param string $string File name.
   *
   * @return string
   */
  public function get_example_file_name( $string ) : string {
    return "{$string}Example";
  }

  /**
   * Open an updated file and create it on output location.
   *
   * @param string $output_dir  Absolute path to output from project root dir.
   * @param string $output_file Absolute path to output file.
   * @param string $class       Modified class.
   *
   * @return void
   */
  public function output_write( string $output_dir, string $output_file, string $class ) : void {

    // Set output paths.
    $output_dir = $this->get_output_dir( $output_dir );

    // Set output file path.
    $output_file = $this->get_output_file( $output_file );
    $output_file = "{$output_dir}{$output_file}";

    // Bailout if file already exists.
    if ( file_exists( $output_file ) ) {
      \WP_CLI::error( "The file {$output_file} can\'t be generated because it already exists." );
    }

    // Create output dir if it doesn't exist.
    if ( ! is_dir( $output_dir ) ) {
      mkdir( $output_dir, 0755, true );
    }

    // Open a new file on output.
    $fp = fopen( $output_file, 'wb' ); // phpcs:ignore WordPress.WP.AlternativeFunctions

    // If there is any error bailout. For example, user permission.
    if ( ! $fp ) {
      \WP_CLI::error( "File {$output_file} couldn\'t be created. There was an error." );
    }

    // Write and close.
    fwrite( $fp, $class ); // phpcs:ignore WordPress.WP.AlternativeFunctions
    fclose( $fp ); // phpcs:ignore WordPress.WP.AlternativeFunctions

    // Return success.
    \WP_CLI::success( "File {$output_file} successfully created." );
  }

  /**
   * Get full output dir path.
   *
   * @param string $path Project specific path.
   *
   * @return string
   */
  public function get_output_dir( string $path = '' ) : string {
    if ( function_exists( 'add_action' ) ) {
      $root = $this->get_project_root_path();
    } else {
      $root = $this->get_project_root_path( true ) . '/cli-output';
    }

    $root = rtrim( $root, '/' );
    $root = trim( $root, '/' );

    $path = rtrim( $path, '/' );
    $path = trim( $path, '/' );

    return "/{$root}/{$path}";
  }

  /**
   * Get full output dir path.
   *
   * @param string $file File name.
   *
   * @return string
   */
  public function get_output_file( string $file ) : string {
    $file = rtrim( $file, '/' );
    $file = trim( $file, '/' );

    if ( strpos( $file, '.' ) !== false ) {
      return "/{$file}";
    }

    return "/{$file}.php";
  }

  /**
   * Replace namespace EightshiftBoilerplateVendor\ in class.
   *
   * @param array  $args   CLI args array.
   * @param string $string Full class as a string.
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
   * @param array  $args   CLI args array.
   * @param string $string Full class as a string.
   *
   * @return string
   *
   * Note: ASCII is used because of composer imposter plugin we are using for prefixing vendors.
   *
   * \x75\x73\x65 - Corresponds to "use".
   */
  public function rename_use( array $args = [], string $string ) : string {

    $output = $string;

    $prefix  = "\x75\x73\x65";
    $pattern = "/{$prefix} (w+|\w+\\\\)";

    $vendor_prefix = $this->get_vendor_prefix( $args );
    $namespace     = $this->get_namespace( $args );

    // Rename all vendor prefix stuff.
    $output = preg_replace(
      "{$pattern}/",
      "{$prefix} {$vendor_prefix}\\",
      $output
    );

    // Leave all project stuff.
    if ( preg_match( "{$pattern}{$namespace}/", $string ) ) {
      $output = preg_replace(
        "{$pattern}{$namespace}/",
        "{$prefix} {$namespace}",
        $output
      );
    }

    return $output;
  }

  /**
   * Replace text domain in class.
   *
   * @param array  $args   CLI args array.
   * @param string $string Full class as a string.
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
   * Replace project file name.
   *
   * @param array  $args   CLI args array.
   * @param string $string Full class as a string.
   *
   * @return string
   */
  public function rename_project_name( array $args = [], string $string ) : string {

    $project_type = 'eightshift-boilerplate';

    if ( isset( $args['project_type'] ) ) {
      $project_type = $args['project_type'];
    }

    return str_replace(
      'eightshift-boilerplate',
      $project_type,
      $string
    );
  }

  /**
   * Replace project file type.
   *
   * @param array  $args   CLI args array.
   * @param string $string Full class as a string.
   *
   * @return string
   */
  public function rename_project_type( array $args = [], string $string ) : string {

    $project_type = 'theme';

    if ( isset( $args['project_type'] ) ) {
      $project_type = "/{$args['project_type']}/";
    }

    return str_replace(
      '/themes/',
      $project_type,
      $string
    );
  }

  /**
   * Change Class full name.
   *
   * @param string $class_name Class Name.
   * @param string $string     Full class as a string.
   *
   * @return string
   */
  public function rename_class_name( string $class_name, string $string ) : string {
    return str_replace( $this->get_example_file_name( $class_name ), $class_name, $string );
  }

  /**
   * Change Class full name with sufix.
   *
   * @param string $template_name Current template.
   * @param string $new_name      New Class Name.
   * @param string $string        Full class as a string.
   *
   * @return string
   */
  public function rename_class_name_with_sufix( string $template_name, string $new_name, string $string ) : string {
    return str_replace( $this->get_example_file_name( $template_name ), $new_name, $string );
  }

  /**
   * Get composer from project or lib.
   *
   * @param array $args CLI args array.
   *
   * @return array
   */
  public function get_composer( array $args = [] ) : array {
    if ( ! isset( $args['config_path'] ) ) {
      if ( function_exists( 'add_action' ) ) {
        $composer_path = $this->get_project_root_path() . '/composer.json';
      } else {
        $composer_path = $this->get_project_root_path( true ) . '/composer.json';
      }
    } else {
      $composer_path = $args['config_path'];
    }

    $composer_file = file_get_contents( $composer_path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

    if ( $composer_file === false ) {
      \WP_CLI::error( "The composer on {$composer_path} path seems to be missing." );
    }

    return json_decode( $composer_file, true );
  }

  /**
   * Get composers defined namespace.
   *
   * @param array $args CLI args array.
   *
   * @return string
   */
  public function get_namespace( array $args = [] ) : string {
    $namespace = '';

    if ( isset( $args['namespace'] ) ) {
      $namespace = $args['namespace'];
    }

    if ( empty( $namespace ) ) {
      $composer = $this->get_composer( $args );

      $namespace = rtrim( $this->array_key_first_child( $composer['autoload']['psr-4'] ), '\\' );
    }

    return $namespace;
  }

  /**
   * Array_key_first polyfill function
   *
   * @param array $array Array to search.
   *
   * @return string
   */
  public function array_key_first_child( array $array ) : string {
    foreach ( $array as $key => $unused ) {
      return $key;
    }

    return '';
  }

  /**
   * Get composers defined vendor prefix.
   *
   * @param array $args CLI args array.
   *
   * @return string
   */
  public function get_vendor_prefix( array $args = [] ) : string {
    $vendor_prefix = '';

    if ( isset( $args['vendor_prefix'] ) ) {
      $vendor_prefix = $args['vendor_prefix'];
    }

    if ( empty( $vendor_prefix ) ) {
      $composer = $this->get_composer( $args );

      $vendor_prefix = $composer['extra']['imposter']['namespace'] ?? 'EightshiftLibs';
    }

    return $vendor_prefix;
  }

  /**
   * Convert user input string to slug safe format. convert _ to -, empty space to - and convert everything to lovercase.
   *
   * @param string $string String to convert.
   *
   * @return string
   */
  public function prepare_slug( string $string ) : string {
    if ( strpos( $string, ' ' ) !== false ) {
      $string = strtolower( $string );
    }

    return str_replace( '_', '-', str_replace( ' ', '-', $string ) );
  }

  /**
   * Loop array of classes and output the commands.
   *
   * @param array $items Array of classes.
   * @param bool  $run   Run or log output.
   * @return void
   */
  public function get_eval_loop( array $items = [], bool $run = false ) : void {
    foreach ( $items as $item ) {
      $reflection_class = new \ReflectionClass( $item );
      $class            = $reflection_class->newInstanceArgs( [ null ] );

      if ( ! $run ) {
        \WP_CLI::log( "wp eval-file bin/cli.php {$class->get_command_name()} --skip-wordpress" );
      } else {
        \WP_CLI::runcommand( "eval-file bin/cli.php {$class->get_command_name()} --skip-wordpress" );
      }
    }
  }

  /**
   * Run reset command in develop mode only.
   *
   * @return void
   */
  public function run_reset() : void {
    $reset = new CliReset( null );
    \WP_CLI::runcommand( "eval-file bin/cli.php {$reset->get_command_name()} --skip-wordpress" );
  }

  /**
   * Returns projects root folder based on the enviroment.
   *
   * @param bool $is_dev Returns path based on the env.
   *
   * @return string
   */
  public function get_project_root_path( bool $is_dev = false ) : string {
    $output = dirname( __DIR__, 5 );

    if ( $is_dev ) {
      $output = dirname( __DIR__, 2 );
    }

    return $output;
  }

  /**
   * Returns projects root where config is instaled based on the enviroment.
   *
   * @param bool $is_dev Returns path based on the env.
   *
   * @return string
   */
  public function get_project_config_root_path( bool $is_dev = false ) : string {
    $output = dirname( __DIR__, 8 );

    if ( $is_dev ) {
      $output = dirname( __DIR__, 2 );
    }

    return $output;
  }

  /**
   * Returns Eightshift frontend libs path.
   *
   * @param string $path Additional path.
   * @return string
   */
  public function get_frontend_libs_path( string $path = '' ) : string {
    return "{$this->get_project_root_path()}/node_modules/@eightshift/frontend-libs/{$path}";
  }

  /**
   * Returns Eightshift libs path.
   *
   * @param string $path Additional path.
   * @return string
   */
  public function get_libs_path( string $path = '' ) : string {
    return "{$this->get_project_root_path()}/vendor/infinum/eightshift-libs/{$path}";
  }

  /**
   * Returns Eightshift frontend libs blocks init path.
   *
   * @return string
   */
  public function get_frontend_libs_block_path() : string {
    return $this->get_frontend_libs_path( 'blocks/init' );
  }
}
