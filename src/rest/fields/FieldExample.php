<?php
/**
 * The class register field for example endpoint
 *
 * @package EightshiftLibs\Rest\Fields
 */

namespace EightshiftLibs\Rest\Fields;

use EightshiftLibs\Rest\CallableFieldInterface;
use EightshiftLibs\Rest\Fields\AbstractField;

/**
 * Class FieldExample
 */
class FieldExample extends AbstractField implements CallableFieldInterface {

  /**
   * Method that returns field object type.
   * Object(s) the field is being registered to, "post"|"term"|"comment" etc.
   *
   * @return string|array
   */
  protected function get_object_type() : string {
    return 'example-post-type';
  }

  /**
   * Get the name of the field you awant to register or orverride.
   *
   * @return string The attribute name.
   */
  protected function get_field_name() : string {
    return 'example-field';
  }

  /**
   * Get callback arguments array
   *
   * @return array Either an array of options for the endpoint, or an array of arrays for multiple methods.
   */
  protected function get_callback_arguments() : array {
    return [
      'get_callback' => [ $this, 'field_callback' ],
    ];
  }

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
   */
  public function field_callback( $object, string $attr, $request, string $object_type ) : string {
    return \rest_ensure_response( 'output data' );
  }
}
