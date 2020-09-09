<?php
/**
 * Class that registers WPCLI command for Setup.
 *
 * @package EightshiftLibs\Setup
 */

namespace EightshiftLibs\Setup;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class SetupCli
 */
class SetupCli extends AbstractCli {

  /**
   * Output dir relative path.
   */
  const OUTPUT_DIR = 'src/config';

  /**
   * Get WPCLI command name
   *
   * @return string
   */
  public function get_command_name() : string {
    return 'init_setup';
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
      'shortdesc' => 'Initialize Command for automatic project setup and update.',
      'synopsis' => [
        [
          'type'        => 'assoc',
          'name'        => 'root',
          'description' => 'Define project root.',
          'optional'    => true,
        ],
      ],
    ];
  }

  public function __invoke( array $args, array $assoc_args ) {

    // Get Props.
    $root = $assoc_args['root'] ?? WP_CLI\Utils\get_home_dir();

    // Read the template contents, and replace the placeholders with provided variables.
    $class = $this->get_example_template( __DIR__, $this->get_class_short_name() );

    // Output final class to new file/folder and finish.
    $this->output_write( $root, $this->get_class_short_name(), $class );
  }
}
