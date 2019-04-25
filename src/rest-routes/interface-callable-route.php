<?php
/**
 * File containing Rest/Api callable interface
 *
 * @since   0.2.0 Rename to callable route.
 * @since   0.1.0
 * @package Eightshift_Libs\Routes
 */

namespace Eightshift_Libs\Routes;

/**
 * Route interface that adds routes
 */
interface Callable_Route {

  /**
   * Method that returns rest response
   *
   * @param  \WP_REST_Request $request Data got from enpoint url.
   *
   * @return WP_REST_Response|mixed If response generated an error, WP_Error, if response
   *                                is already an instance, WP_HTTP_Response, otherwise
   *                                returns a new WP_REST_Response instance.
   *
   * @since 0.1.0
   */
  public function route_callback( \WP_REST_Request $request );
}
