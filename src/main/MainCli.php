<?php
/**
 * Class that registers WPCLI command for Main.
 *
 * @package EightshiftLibs\Main
 */

declare( strict_types=1 );

namespace EightshiftLibs\Main;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class MainCli
 */
class MainCli extends AbstractCli {

  /**
   * Output dir relative path.
   */
  const OUTPUT_DIR = 'src/main';

  /**
   * Get WPCLI command doc.
   *
   * @return string
   */
  public function get_doc() : array {
    return [
      'shortdesc' => 'Generates Main class file for all other features using service container pattern.',
    ];
  }

  public function __invoke( array $args, array $assoc_args ) { // phpcs:ignore Squiz.Commenting.FunctionComment.Missing, Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClassBeforeLastUsed

    // Read the template contents, and replace the placeholders with provided variables.
    $class = $this->get_example_template( __DIR__, $this->get_class_short_name() );

    // Replace stuff in file.
    $class = $this->rename_class_name( $this->get_class_short_name(), $class );
    $class = $this->rename_namespace( $assoc_args, $class );
    $class = $this->rename_use( $assoc_args, $class );

    // Output final class to new file/folder and finish.
    $this->output_write( static::OUTPUT_DIR, $this->get_class_short_name(), $class );
  }
}
