<?php
/**
 * File that holds the renderable interface.
 *
 * @since   0.1.0
 * @package Eightshift_Libs\Core
 */

namespace Eightshift_Libs\Core;

/**
 * Interface Renderable.
 *
 * An object that can be rendered.
 */
interface Renderable {

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
