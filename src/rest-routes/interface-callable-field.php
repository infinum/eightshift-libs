<?php
/**
 * File containing Rest/Api field callable interface
 *
 * @since   0.1.0
 * @package Eightshift_Libs\Routes
 */

namespace Eightshift_Libs\Routes;

/**
 * Field interface that adds/extends fields in routes.
 */
interface Callable_Field {

  /**
   * Method that returns rest response
   *
   * @param object|array $object      Post or custom post type object of the request.
   * @param string       $attr        Rest field/attr string identifier from the second parameter of your register_rest_field() declaration.
   * @param object       $request     Full request payload – as a WP_REST_Request object.
   * @param string       $object_type The object type which the field is registered against. Typically first parameter of your register_rest_field() declaration.
   *
   * @return mixed If response generated an error, WP_Error, if response
   *               is already an instance, WP_HTTP_Response, otherwise
   *               returns a new WP_REST_Response instance.
   *
   * @since 0.8.0 Removing type hinting void for php 7.0.
   * @since 0.2.0 Removed type hinting from first argument because it can be object|array.
   * @since 0.1.0
   */
  public function field_callback( $object, string $attr, $request, string $object_type );
}
