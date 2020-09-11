<?php
/**
 * Class that registers WPCLI command for CiExcludeCli.
 *
 * @package EightshiftLibs\CiExclude
 */

namespace EightshiftLibs\CiExclude;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class CiExcludeCli
 */
class CiExcludeCli extends AbstractCli {

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
    return 'init_ci_exclude';
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
      'shortdesc' => 'Initialize Command for building your projects CI exclude file.',
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
    $class = $this->get_example_template( __DIR__, 'ci-exclude.txt' );

    // Output final class to new file/folder and finish.
    $this->output_write( $root, 'ci-exclude.txt', $class );
  }
}
