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
 * Interface Attributes_Data
 *
 * @since 1.0.0
 */
interface Attributes_Data {

  /**
   * Get blocks attributes.
   * This method combines default, block and shared attributes.
   * Default attributes are hardcoded in this lib.
   * Block attributes are provided by block manifest.json file.
   * Shared attributes are provided by blocks settings manifest.json file and is only available if block has `hasWrapper:true` settings.
   *
   * @param array $block_details Block Manifest details.
   *
   * @return array
   */
  public function get_attributes( array $block_details ) : array;
}
