<?php
/**
 * Class Attributes holds all attributes definitions for blocks.
 *
 * @since   1.0.0
 * @package Eightshift_Blocks
 */

namespace Eightshift_Blocks;

use Eightshift_Blocks\Blocks_Full_Data;
use Eightshift_Blocks\Attributes;
use Eightshift_Blocks\Exception\Missing_Block_Wrapper_View;
use Eightshift_Blocks\Exception\Missing_Block_View;

/**
 * Class Attributes
 *
 * @since 1.0.0
 */
abstract class Attributes extends Blocks_Full_Data implements Attributes_Data {

  /**
   * Get blocks attributes.
   * This method combines default, block and wrapper attributes.
   * Default attributes are hardcoded in this lib.
   * Block attributes are provided by block manifest.json file.
   * Wrapper attributes are provided by wrapper manifest.json file and is only available if block has `hasWrapper:true` settings.
   *
   * @param array $block_details Block Manifest details.
   *
   * @return array
   */
  public function get_attributes( array $block_details ) : array {

    $default_attributes      = $this->get_default_attributes( $block_details );
    $block_attributes        = $this->get_block_attributes( $block_details );
    $block_shared_attributes = ( $block_details['hasWrapper'] === true ) ? $this->get_wrapper_attributes() : [];

    return array_merge(
      $default_attributes,
      $block_attributes,
      $block_shared_attributes
    );
  }

  /**
   * Get blocks shared attributes value from blocks settings.
   *
   * @return string
   *
   * @since 1.0.0
   */
  protected function get_wrapper_attributes() : array {
    $wrapper_settings = $this->get_wrapper();

    return $wrapper_settings['attributes'] ?? [];
  }

  /**
   * Get blocks attributes value.
   *
   * @param array $block_details Block Manifest details.
   *
   * @return string
   *
   * @since 1.0.0
   */
  protected function get_block_attributes( array $block_details ) : array {
    return $block_details['attributes'] ?? [];
  }

  /**
   * Get default attributes that are dynamically built for all custom blocks.
   * These are:
   * - blockName: Block's name (example: heading)
   * - blockFullName: Block's full name including namespace (example: eightshift-blocks/heading)
   * - blockClass: Block's root (base) BEM CSS class, built in "block/$name" format (example: block-heading)
   * - blockJsClass:   Block's js selector class, built in "js-block-$name" format (example: js-block-heading)
   *
   * @param array $block_details Block Manifest details.
   *
   * @return array
   *
   * @since 1.0.0
   */
  protected function get_default_attributes( array $block_details ) : array {
    $block_name      = $this->get_block_name( $block_details );
    $block_full_name = $this->get_block_full_name( $block_details );

    return [
      'blockName' => array(
        'type' => 'string',
        'default' => $block_name,
      ),
      'blockFullName' => array(
        'type' => 'string',
        'default' => $block_full_name,
      ),
      'blockClass' => array(
        'type' => 'string',
        'default' => "block-{$block_name}",
      ),
      'blockJsClass' => array(
        'type' => 'string',
        'default' => "js-block-{$block_name}",
      ),
    ];
  }
}
