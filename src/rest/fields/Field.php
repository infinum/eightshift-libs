<?php
/**
 * The class register field for example endpoint
 *
 * @since   1.0.0
 * @package EightshiftBoilerplate\Rest\Fields
 */

namespace EightshiftBoilerplate\Rest\Fields;

use EightshiftLibs\Rest\CallableFieldInterface;
use EightshiftLibs\Rest\Fields\AbstractField;

/**
 * Class Register Field
 */
class Field extends AbstractField implements CallableFieldInterface {

  /**
   * A register method holds register_rest_route funtion to register or override api field.
   *
   * @return void
   */
  public function register() : void {
    add_action(
      'rest_api_init',
      function() {
        register_rest_field(
          $this->get_object_type(),
          $this->get_field_name(),
          $this->get_callback_arguments()
        );
      }
    );
  }

  /**
   * Method that returns field object type.
   * Object(s) the field is being registered to, "post"|"term"|"comment" etc.
   *
   * @return string|array
   *
   * @since 2.0.0 Added in the project
   */
  protected function get_object_type() : string {
    return 'post';
  }

  /**
   * Get the name of the field you awant to register or orverride.
   *
   * @return string The attribute name.
   *
   * @since 2.0.0 Added in the project
   */
  protected function get_field_name() : string {
    return 'example-field-name';
  }

  /**
   * Get callback arguments array
   *
   * @return array Either an array of options for the endpoint, or an array of arrays for multiple methods.
   *
   * @since 2.0.0 Added in the project
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
   *
   * @since 0.8.0 Removing type hinting void for php 7.0.
   * @since 0.2.0 Removed type hinting from first argument because it can be object|array.
   * @since 0.1.0
   */
  public function field_callback( $object, string $attr, $request, string $object_type ) : string {
    return \rest_ensure_response( 'output data' );
  }
}
