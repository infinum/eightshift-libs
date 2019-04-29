<?php
/**
 * File that holds General_Block abstract class for Gutenberg blocks registration.
 * This implementation is used for normal block setup.
 *
 * @since   0.3.0
 * @package Eightshift_Libs\Blocks
 */

namespace Eightshift_Libs\Blocks;

use Eightshift_Libs\Exception\Missing_Block;

/**
 * Class Block
 */
abstract class General_Block extends Base_Block {

  /**
   * Get all block attributes. Default and block attributes.
   *
   * @return array
   *
   * @since 0.3.0
   */
  public function get_attributes() : array {
    return array_merge( $this->get_default_attributes(), $this->get_block_attributes() );
  }

  /**
   * Renders the block using a template in Infinum\Blocks\Templates namespace/folder.
   * Template file must have the same name as the class-blockname file, for example:
   *
   *   Block:     class-heading.php
   *   Template:  heading.php
   *
   * @param array  $attributes          Array of attributes as defined in block's index.js.
   * @param string $inner_block_content Block's content.
   *
   * @throws Missing_Block::view_exception On missing attributes OR missing template.
   * @return string html template for block.
   *
   * @since 0.5.0 Changed $content to $inner_block_content for better naming.
   * @since 0.3.0
   */
  public function render( array $attributes, ?string $inner_block_content ) : string {
    $template_path = $this->get_block_view_path();

    $template = locate_template( $template_path );
    if ( empty( $template ) ) {
      throw Missing_Block::view_exception( $this->get_block_name(), $template_path );
    }

    // If everything is ok, return the contents of the template (return, NOT echo).
    ob_start();
    include $template;
    $output = ob_get_clean();
    unset( $attributes, $inner_block_content, $template );
    return $output;
  }
}
