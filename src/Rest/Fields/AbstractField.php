<?php
/**
 * The class file that holds abstract class for REST fields registration.
 *
 * @package EightshiftLibs\Rest
 */

declare( strict_types=1 );

namespace EightshiftLibs\Rest\Fields;

use EightshiftLibs\Services\ServiceInterface;

/**
 * Abstract base field class
 */
abstract class AbstractField implements ServiceInterface {

  /**
   * A register method holds register_rest_route funtion to register or override api field.
   *
   * @return void
   */
  public function register() : void {
    \add_action( 'rest_api_init', [ $this, 'field_register_callback' ] );
  }

  /**
   * Method that register rest field that is used inside rest_api_init hook.
   *
   * @return void
   */
  protected function field_register_callback() : void {
    \register_rest_field(
      $this->get_object_type(),
      $this->get_field_name(),
      $this->get_callback_arguments()
    );
  }

  /**
   * Method that returns field object type.
   * Object(s) the field is being registered to, "post"|"term"|"comment" etc.
   *
   * @return string|array
   */
  abstract protected function get_object_type();

  /**
   * Get the name of the field you awant to register or orverride.
   *
   * @return string The attribute name.
   */
  abstract protected function get_field_name() : string;

  /**
   * Get callback arguments array
   *
   * @return array Either an array of options for the endpoint, or an array of arrays for multiple methods.
   */
  abstract protected function get_callback_arguments() : array;
}
