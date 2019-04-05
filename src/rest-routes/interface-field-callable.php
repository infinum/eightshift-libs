<?php
/**
 * File containing field callable interface
 *
 * @since 1.0.0
 * @package Eightshift_Libs\Routes
 */

namespace Eightshift_Libs\Routes;

/**
 * Field interface that adds/extends fields in routes
 */
interface Field_Callable {

  /**
   * Method that returns rest response
   *
   * @param  object $object      Post or custom post type object of the request.
   * @param  string $attr        Rest field/attr string identifier from the second parameter of your register_rest_field() declaration.
   * @param  object $request     Full request payload – as a WP_REST_Request object.
   * @param  string $object_type The object type which the field is registered against. Typically first parameter of your register_rest_field() declaration.
   * @return mixed          If response generated an error, WP_Error, if response
   *                        is already an instance, WP_HTTP_Response, otherwise
   *                        returns a new WP_REST_Response instance.
   */
  public function field_callback( object $object, string $attr, object $request, string $object_type );
}
