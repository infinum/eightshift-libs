<?php
/**
 * File that holds Wrapper_Block abstract class for Gutenberg blocks registration.
 * This implementation is used if you want to add markup around you block that is reused in the project.
 *
 * @since   0.4.0 Changing wrapper block view path.
 * @since   0.3.0
 * @package Eightshift_Libs\Blocks
 */

namespace Eightshift_Libs\Blocks;

use Eightshift_Libs\Exception\Missing_Block;

/**
 * Class Block
 */
abstract class Wrapper_Block extends Base_Block {

  /**
   * Get block wrapper view path.
   *
   * @return string
   *
   * @since 0.4.0 Changing view path.
   * @since 0.3.0
   */
  public function get_block_wrapper_view_path() {
    $block_name = $this->get_block_name();

    return 'src/blocks/wrapper-block/view.php';
  }

  /**
   * Get all block attributes. Default, block and shared attributes.
   *
   * @return array
   *
   * @since 0.3.0
   */
  public function get_attributes() : array {
    return array_merge( $this->get_default_attributes(), $this->get_block_attributes(), $this->get_block_shared_attributes() );
  }


  /**
   * Define shared block attributes that will all block share.
   *
   * @return array
   *
   * @since 0.3.0
   */
  abstract protected function get_block_shared_attributes() : array;

  /**
   * Renders the block using a template in Infinum\Blocks\Templates namespace/folder.
   * Template file must have the same name as the class-blockname file, for example:
   *
   *   Block:     class-heading.php
   *   Template:  heading.php
   *
   * @param array  $attributes Array of attributes as defined in block's index.js.
   * @param string $content    Block's content.
   *
   * @throws Missing_Block::view_exception On missing attributes OR missing template.
   * @return string html template for block.
   *
   * @since 0.3.0
   */
  public function render( array $attributes, string $content ) : string {
    $template_path = $this->get_block_view_path();
    $wrapper_path  = $this->get_block_wrapper_view_path();

    $wrapper = locate_template( $wrapper_path );
    if ( empty( $wrapper ) ) {
      throw Missing_Block::view_exception( esc_html__( 'Wrapper Block', 'eightshift-libs' ), $wrapper_path );
    }

    $template = locate_template( $template_path );
    if ( empty( $template ) ) {
      throw Missing_Block::view_exception( $this->get_block_name(), $template_path );
    }

    // If everything is ok, return the contents of the template (return, NOT echo).
    ob_start();
    include $wrapper;
    $output = ob_get_clean();
    unset( $wrapper, $template, $template_path, $wrapper_path );
    return $output;
  }
}
