<?php
/**
 * Class that registers WPCLI command for Setup.
 *
 * @package EightshiftLibs\Setup
 */

namespace EightshiftLibs\Setup;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class UpdateCli
 */
class UpdateCli extends AbstractCli {

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
    return 'update';
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
      'shortdesc' => 'Run project update with detailes stored in setup.json file.',
      'synopsis' => [
        [
          'type'        => 'assoc',
          'name'        => 'root',
          'description' => 'Define project root relative to initialization file of WP CLI.',
          'optional'    => true,
        ],
      ],
    ];
  }

  public function __invoke( array $args, array $assoc_args ) {

    // Get Props.
    $root = $assoc_args['root'] ?? static::OUTPUT_DIR;

    // Read the template contents, and replace the placeholders with provided variables.
    $class = $this->get_example_template( __DIR__, $this->get_class_short_name() );

    // $reflection_class = new \ReflectionClass( $item );
    // $class            = $reflection_class->newInstanceArgs( [ null ] );

    \WP_CLI::runcommand( "{$this->command_parent_name} {$class->get_command_name()}" );
  }
}
