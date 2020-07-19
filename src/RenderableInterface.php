<?php
/**
 * File that holds the renderable interface.
 *
 * @package EightshiftLibs\Core
 */

declare( strict_types=1 );

namespace EightshiftLibs\Core;

/**
 * Interface Renderable.
 *
 * An object that can be rendered.
 */
interface RenderableInterface {

  /**
   * Render the current Renderable.
   *
   * @param array $context Context in which to render.
   *
   * @return string Rendered HTML.
   */
  public function render( array $context = [] ) : string;
}
