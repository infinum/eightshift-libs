<?php
/**
 * File that holds the renderable Block interface.
 *
 * @since   1.0.0
 * @package EightshiftLibs\Blocks
 */

declare( strict_types=1 );

namespace EightshiftLibs\Blocks;

/**
 * Interface Renderable Block.
 *
 * An object that can be rendered.
 *
 * @since 2.0.2 Fixing wrong type hinting for $inner_block_content.
 * @since 1.0.0
 */
interface RenderableBlockInterface {

  /**
   * Provides block registration render callback method.
   *
   * @param array  $attributes          Array of attributes as defined in block's manifest.json.
   * @param string $inner_block_content Block's content if using inner blocks.
   *
   * @throws \Exception On missing attributes OR missing template.
   * @return string
   *
   * @since 1.0.0
   */
  public function render( array $attributes, $inner_block_content ) : string;
}
