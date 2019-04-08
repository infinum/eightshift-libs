<?php
/**
 * Interface for blocks.
 *
 * @since 1.0.0
 * @package Eightshift_Libs\Blocks
 */

namespace Eightshift_Libs\Blocks;

/**
 * Interface Block
 */
interface Block {

  /**
   * Adds default attributes that are dynamically built for all blocks.
   * These are:
   * - blockName: Block's full name including namespace (example: Infinum/heading)
   * - rootClass: Block's root (base) BEM CSS class, built in "block/$name" format (example: block-heading)
   * - jsClass:   Block's js selector class, built in "js-block-$name" format (example: js-block-heading)
   *
   * @return array
   *
   * @since 1.0.0
   */
  public function get_default_attributes() : array;

  /**
   * Get block attributes assigned inside block class.
   *
   * @return array
   *
   * @since 1.0.0
   */
  public function get_block_attributes() : array;

  /**
   * Get all block attributes. Default and block attributes.
   *
   * @return array
   *
   * @since 1.0.0
   */
  public function get_attributes() : array;

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
   * @echo string
   *
   * @since 1.0.0
   */
  public function render( array $attributes, string $content ) : string;
}
