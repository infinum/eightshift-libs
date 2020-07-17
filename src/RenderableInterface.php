<?php
/**
 * File that holds the renderable interface.
 *
 * @package Eightshiftlibs\Core
 */

declare( strict_types=1 );

namespace Eightshiftlibs\Core;

/**
 * Interface Renderable.
 *
 * An object that can be rendered.
 *
 * @since 0.1.0
 */
interface RenderableInterface {

  /**
   * Render the current Renderable.
   *
   * @param array $context Context in which to render.
   *
   * @return string Rendered HTML.
   *
   * @since 0.1.0
   */
  public function render( array $context = [] ) : string;
}
