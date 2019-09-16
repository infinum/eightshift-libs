<?php
/**
 * Projects Assets manifest data interface.
 *
 * Used to define the way manifest item is retrieved from the manifest file.
 *
 * @since   1.0.0
 * @package Eightshift_Blocks
 */

namespace Eightshift_Blocks;

/**
 * Interface Manifest_Data
 *
 * @since 1.0.0
 */
interface Manifest_Data {

  /**
   * Method to provide projects manifest array.
   * Using this manifest you are able to provide project specific implementation of assets locations.
   *
   * @return array
   *
   * @since 1.0.0
   */
  public function get_project_manifest() : array;
}
