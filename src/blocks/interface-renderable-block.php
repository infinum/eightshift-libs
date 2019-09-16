<?php
/**
 * File that holds the renderable Block interface.
 *
 * @since   1.0.0
 * @package Eightshift_Blocks
 */

namespace Eightshift_Blocks;

/**
 * Interface Renderable Block.
 *
 * An object that can be rendered.
 *
 * @since 1.0.0
 */
interface Renderable_Block {

  /**
   * Provides block registration render callback method.
   *
   * @param array  $attributes Array of attributes as defined in block's manifest.json.
   * @param string $content    Block's content.
   *
   * @throws \Exception On missing attributes OR missing template.
   * @return string
   *
   * @since 1.0.0
   */
  public function render( array $attributes, string $content ) : string;
}
