<?php
/**
 * Class that registers WPCLI command for Blocks.
 *
 * @package EightshiftLibs\Blocks
 */

declare( strict_types=1 );

namespace EightshiftLibs\Blocks;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class BlocksCli
 */
class BlocksCli extends AbstractCli {

  /**
   * Output dir relative path.
   */
  const OUTPUT_DIR = 'src/blocks';

  const COMPONENTS = [
    'button',
    'heading',
    'image',
    'link',
    'lists',
    'paragraph',
    'tracking',
    'video',
    'google-rich-snippets',
    'header',
    'footer',
    'logo',
    'drawer',
    'menu',
    'hamburger',
    'copyright',
    'page-overlay',
  ];

  const BLOCKS = [
    'button',
    'heading',
    'image',
    'link',
    'lists',
    'paragraph',
    'video',
    'example',
  ];

  /**
   * Get WPCLI command doc.
   *
   * @return string
   */
  public function get_doc() : array {
    return [
      'shortdesc' => 'Generates Blocks class.',
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

    if ( function_exists( 'add_action' ) ) {
      $this->blocks_init();
    }
  }

  /**
   * Copy blocks from Eightshift-frontend-libs to project.
   *
   * @param bool $all Copy all from Eightshift-frontend-libs to project or selective from the list.
   *
   * @return void
   */
  public function blocks_init( bool $all = false ) : void {
    $root      = $this->get_project_root_path();
    $root_node = $this->get_frontend_libs_block_path();

    system( "cp -R {$root_node}/assets {$root}/assets" );
    system( "cp -R {$root_node}/storybook {$root}/.storybook" );

    if ( $all ) {
      system( "cp -R {$root_node}/src/blocks {$root}/src/blocks" );
    } else {
      system( "cp -R {$root_node}/src/blocks/assets {$root}/src/blocks/assets/" );
      system( "cp -R {$root_node}/src/blocks/variations {$root}/src/blocks/variations/" );
      system( "cp -R {$root_node}/src/blocks/wrapper {$root}/src/blocks/wrapper/" );
      system( "cp -R {$root_node}/src/blocks/manifest.json {$root}/src/blocks/" );

      foreach ( static::COMPONENTS as $component ) {
        system( "mkdir -p {$root}/src/blocks/components/{$component}/" );
        system( "cp -R {$root_node}/src/blocks/components/{$component}/. {$root}/src/blocks/components/{$component}/" );
      }

      foreach ( static::BLOCKS as $block ) {
        system( "mkdir -p {$root}/src/blocks/custom/{$block}/" );
        system( "cp -R {$root_node}/src/blocks/custom/{$block}/. {$root}/src/blocks/custom/{$block}/" );
      }
    }

    \WP_CLI::success( 'Blocks successfully set.' );
  }
}
