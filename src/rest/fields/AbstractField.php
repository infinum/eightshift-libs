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
