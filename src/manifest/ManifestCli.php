<?php
/**
 * Class that registers WPCLI command for Manifest.
 *
 * @package EightshiftLibs\Manifest
 */

declare( strict_types=1 );

namespace EightshiftLibs\Manifest;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class ManifestCli
 */
class ManifestCli extends AbstractCli {

  /**
   * Output dir relative path.
   */
  const OUTPUT_DIR = 'src/manifest';

  /**
   * Get WPCLI command doc.
   *
   * @return string
   */
  public function get_doc() : array {
    return [
      'shortdesc' => 'Generates Manifest class.',
    ];
  }

  public function __invoke( array $args, array $assoc_args ) { // phpcs:ignore Squiz.Commenting.FunctionComment.Missing, Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClassBeforeLastUsed

    $class_name = $this->get_class_short_name();

    // Read the template contents, and replace the placeholders with provided variables.
    $class = $this->get_example_template( __DIR__, $class_name );

    // Replace stuff in file.
    $class = $this->rename_class_name( $class_name, $class );
    $class = $this->rename_namespace( $assoc_args, $class );
    $class = $this->rename_use( $assoc_args, $class );

    // Output final class to new file/folder and finish.
    $this->output_write( static::OUTPUT_DIR, $class_name, $class );
  }
}
