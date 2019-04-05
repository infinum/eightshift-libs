<?php
/**
 * File that holds Has_Activation interface
 *
 * @since 1.0.0
 * @package Eightshift_Libs\Core
 */

namespace Eightshift_Libs\Core;

/**
 * Interface Service.
 *
 * A generic service. Service is a part of the plugin functionality.
 *
 * @since 1.0.0
 */
interface Service extends Registrable {
  /**
   * Theme Name Constant
   *
   * @var string
   *
   * @since 1.0.0
   */
  const THEME_NAME = 'infinum';

  /**
   * Theme Version Constant
   *
   * @var string
   *
   * @since 1.0.0
   */
  const THEME_VERSION = '1.0.0';
}
