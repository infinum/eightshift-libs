<?php
/**
 * Class that registers WPCLI command for BuildCli.
 *
 * @package EightshiftLibs\Build
 */

namespace EightshiftLibs\Build;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class BuildCli
 */
class BuildCli extends AbstractCli {

  /**
   * Output dir relative path.
   */
  const OUTPUT_DIR = '../../../';

  /**
   * Get WPCLI command name
   *
   * @return string
   */
  public function get_command_name() : string {
    return 'init_build';
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
      'root' => $args[1] ?? './',
    ];
  }

  /**
   * Get WPCLI command doc.
   *
   * @return string
   */
  public function get_doc() : array {
    return [
      'shortdesc' => 'Initialize Command for building your project with one command, generally used on CI deployments.',
      'synopsis' => [
        [
          'type'        => 'assoc',
          'name'        => 'root',
          'description' => 'Define project root relative to initialization file of WP CLI.',
          'optional'    => true,
        ],
        [
          'type'        => 'assoc',
          'name'        => 'skip_setup_file',
          'description' => 'If you already have setup.json file in the root of your project.',
          'optional'    => true,
        ],
      ],
    ];
  }

  public function __invoke( array $args, array $assoc_args ) {

    // Get Props.
    $root            = $assoc_args['root'] ?? static::OUTPUT_DIR;
    $skip_setup_file = $assoc_args['skip_setup_file'] ?? true;

    // Read the template contents, and replace the placeholders with provided variables.
    $class = $this->get_example_template( __DIR__, $this->get_class_short_name() );

    // Output final class to new file/folder and finish.
    $this->output_write( $root . 'bin', $this->get_class_short_name(), $class );

    if ( ! $skip_setup_file ) {
      // Get setup.json file.
      $json = $this->get_example_template( dirname( __DIR__, 1 ), 'setup/setup.json' );

      // Output json file to project root.
      $this->output_write( $root, 'setup.json', $json );
    }
  }
}
