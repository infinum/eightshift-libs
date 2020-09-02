<?php
/**
 * Class that registers WPCLI command for Blocks Components.
 *
 * @package EightshiftLibs\Blocks
 */

namespace EightshiftLibs\Blocks;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class BlocksComponentCli
 */
class BlocksComponentCli extends AbstractCli {

  /**
   * Output dir relative path.
   */
  const OUTPUT_DIR = 'src/blocks';

  /**
   * Get WPCLI command doc.
   *
   * @return string
   */
  public function get_doc() : array {
    return [
      'shortdesc' => 'Generates Blocks Component class.',
      'synopsis' => [
        [
          'type'        => 'assoc',
          'name'        => 'component',
          'description' => 'Specify component name.',
          'optional'    => true,
        ],
      ],
    ];
  }

  public function __invoke( array $args, array $assoc_args ) {

    // Get Props.
    $component = $assoc_args['component'] ?? '';

    $root     = $this->get_project_root_path();
    $rootNode = $this->get_frontend_libs_block_path();

    $source_path = "{$rootNode}/src/blocks/components/{$component}";
    $destination_path = "{$root}/src/blocks/components/{$component}";

    // Source doesn't exist.
    if ( ! file_exists( $source_path ) ) {
      \WP_CLI::error(
        sprintf( 'The component "%s" doesn\'t exist in our library. Please check the docs for all available components', $source_path )
      );
    }

    // Destination exists.
    if ( file_exists( $destination_path ) ) {
      \WP_CLI::error(
        sprintf( 'The component in you project exists on this "%s" path. Please check or remove that folder before running this command again.', $destination_path )
      );
    }

    system( "cp -R {$source_path}/. {$destination_path}/");

    \WP_CLI::success( 'Component successfully created.' );
  }
}
