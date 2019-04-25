<?php
/**
 * File that holds all block attribute types used as enums.
 *
 * @since   0.1.0
 * @package Eightshift_Libs\Blocks
 */

namespace Eightshift_Libs\Blocks;

/**
 * Class Attribute_Type_Enums
 */
abstract class Attribute_Type_Enums {

  /**
   * Type String Name.
   *
   * @var string
   *
   * @since 0.1.0
   */
  const TYPE_STRING = 'string';

  /**
   * Type Number Name.
   *
   * @var string
   *
   * @since 0.1.0
   */
  const TYPE_NUMBER = 'number';

  /**
   * Type Bool Name.
   *
   * @var string
   *
   * @since 0.1.0
   */
  const TYPE_BOOL = 'bool';

  /**
   * Type Array Name.
   *
   * @var string
   *
   * @since 0.1.0
   */
  const TYPE_ARRAY = 'array';

  /**
   * Type Object Name.
   *
   * @var string
   *
   * @since 0.1.0
   */
  const TYPE_OBJECT = 'object';
}
