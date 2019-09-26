<?php
/**
 * Project Config data interface.
 *
 * Used to define the way Config item is retrieved from the Config file.
 *
 * @package Eightshift_Libs\Core
 */

namespace Eightshift_Libs\Core;

/**
 * Interface Config_Data
 *
 * @since 2.0.0
 */
interface Config_Data {

  /**
   *
   * Return project prefix for adding it to all the filters as a prefix because all filters in WordPress live inside a global namespace.
   *
   * @return string Full path to asset.
   *
   * @since 2.0.0 Init
   */
  public static function get_project_prefix() : string;

  /**
   * Return project absolute path for theme use get_template_directory() and for plugin use __DIR__.
   *
   * @return string
   *
   * @since 2.0.0
   */
  public function get_project_path() : string;
}
