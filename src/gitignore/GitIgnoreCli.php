<?php
/**
 * Class that registers WPCLI command for GitIgnoreCli.
 *
 * @package EightshiftLibs\GitIgnore
 */

declare( strict_types=1 );

namespace EightshiftLibs\GitIgnore;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class GitIgnoreCli
 */
class GitIgnoreCli extends AbstractCli {

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
    return 'init_gitignore';
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
      'shortdesc' => 'Initialize Command for building your projects gitignore.',
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

  public function __invoke( array $args, array $assoc_args ) { // phpcs:ignore Squiz.Commenting.FunctionComment.Missing, Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClassBeforeLastUsed

    // Get Props.
    $root = $assoc_args['root'] ?? static::OUTPUT_DIR;

    // Read the template contents, and replace the placeholders with provided variables.
    $class = $this->get_example_template( __DIR__, '.gitignore' );

    // Output final class to new file/folder and finish.
    $this->output_write( $root, '.gitignore', $class );
  }
}
