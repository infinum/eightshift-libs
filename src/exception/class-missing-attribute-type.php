<?php
/**
 * Class Missing_Attribute_Type provides Exception if block attribute type is not foud.
 *
 * @since   1.0.0
 * @package Eightshift_Blocks\Exception
 */

namespace Eightshift_Blocks\Exception;

/**
 * Class Missing_Attribute_Type.
 *
 * @since 1.0.0
 */
class Missing_Attribute_Type extends \InvalidArgumentException implements General_Exception {

  /**
   * Create a new instance of the exception for an missing block name.
   *
   * @param string $block_name Block name.
   * @param string $attribute_name Block attribute name.
   *
   * @return static
   *
   * @since 1.0.0
   */
  public static function type_exception( string $block_name, string $attribute_name ) {
    $message = sprintf(
      esc_html__( 'Missing block attribute type in block manifest. Block Name: %1$s, Block Attribute: %2$s', 'eightshift-blocks' ),
      $block_name,
      $attribute_name
    );
    return new static( $message );
  }
}
