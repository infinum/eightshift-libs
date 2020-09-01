<?php
/**
 * Class that registers WPCLI command for Config.
 * 
 * Command Develop:
 * wp eval-file bin/cli.php create_config --skip-wordpress
 *
 * @package EightshiftLibs\Config
 */

namespace EightshiftLibs\Config;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class ConfigCli
 */
class ConfigCli extends AbstractCli {

  /**
   * Output dir relative path.
   */
  const OUTPUT_DIR = 'src/config';

  /**
   * Output class name.
   */
  const CLASS_NAME = 'Config';

  /**
   * Get WPCLI command name
   *
   * @return string
   */
  public static function get_command_name() : string {
    return 'create_config';
  }

  /**
   * Get WPCLI trigger class name.
   *
   * @return string
   */
  public function get_class_name() : string {
    return ConfigCli::class;
  }

 /**
   * Define default develop props.
   *
   * @param array $args WPCLI eval-file arguments.
   *
   * @return array
   */
  public function get_develop_args( array $args ) : array {
    return [
      'name'           => $args[1] ?? 'Boilerplate',
      'version'        => $args[2] ?? '1',
      'prefix'         => $args[3] ?? 'ebs',
      'env'            => $args[4] ?? 'EBS_ENV',
      'routes_version' => $args[5] ?? 'v2',
    ];
  }

  /**
   * Get WPCLI command doc.
   *
   * @return string
   */
  public function get_doc() : array {
    return [
      'shortdesc' => 'Generates project config class.',
      'synopsis' => [
        [
          'type'        => 'assoc',
          'name'        => 'name',
          'description' => 'Define project name.',
          'optional'    => true,
        ],
        [
          'type'        => 'assoc',
          'name'        => 'version',
          'description' => 'Define project name.',
          'optional'    => true,
        ],
        [
          'type'        => 'assoc',
          'name'        => 'prefix',
          'description' => 'Define project prefix.',
          'optional'    => true,
        ],
        [
          'type'        => 'assoc',
          'name'        => 'env',
          'description' => 'Define project env.',
          'optional'    => true,
        ],
        [
          'type'        => 'assoc',
          'name'        => 'routes_version',
          'description' => 'Define project env.',
          'optional'    => true,
        ],
      ],
    ];
  }

  public function __invoke( array $args, array $assoc_args ) {

    // Get Props.
    $name           = $assoc_args['name'] ?? '';
    $version        = $assoc_args['version'] ?? '';
    $prefix         = $assoc_args['prefix'] ?? '';
    $env            = $assoc_args['env'] ?? '';
    $routes_version = $assoc_args['routes_version'] ?? '';

    // Read the template contents, and replace the placeholders with provided variables.
    $class = $this->get_example_template( __DIR__, static::CLASS_NAME );

    // Replace stuff in file.
    $class = $this->rename_class_name( static::CLASS_NAME, $class );
    $class = $this->rename_namespace( $assoc_args, $class );
    $class = $this->rename_use( $assoc_args, $class );

    if ( ! empty( $name ) ) {
      $class = str_replace( "eightshift-libs", $name, $class );
    }

    if ( ! empty( $version ) ) {
      $class = str_replace( "1.0.0", $version, $class );
    }

    if ( ! empty( $prefix ) ) {
      $class = str_replace( "'eb'", "'{$prefix}'", $class );
    }

    if ( ! empty( $env ) ) {
      $class = str_replace( "EB_ENV", $env, $class );
    }

    if ( ! empty( $routes_version ) ) {
      $class = str_replace( "v1", $routes_version, $class );
    }

    // Output final class to new file/folder and finish.
    $this->output_write( static::OUTPUT_DIR, static::CLASS_NAME, $class );
  }
}
