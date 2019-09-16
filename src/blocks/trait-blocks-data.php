<?php
/**
 * Class Blocks_Data holds registration of global variable for cache and storing all blocks manifest data.
 *
 * @since   1.0.0
 * @package Eightshift_Blocks
 */

namespace Eightshift_Blocks;

use Eightshift_Blocks\Exception\Missing_Block_Name;

/**
 * Class Blocks_Data
 *
 * @since 1.0.0
 */
trait Blocks_Data {

  /**
   * Default projects blocks global variable name.
   * Used to store all blocks data.
   *
   * @var string
   *
   * @since 1.0.0
   */
  protected $blocks_variable_name = 'ES_BLOCKS';

  /**
   * Define global variable for all custom blocks.
   * Used to cache data inside a global variable so you don't have to fetch manifest.json file on every call.
   *
   * @return void
   *
   * @since 1.0.0
   */
  public function register_blocks_variable() {
    define( $this->get_blocks_variable_name(), $this->get_blocks_raw() );
  }

  /**
   * Get global blocks variable name to store the cached data into.
   *
   * @return string
   *
   * @since 1.0.0
   */
  protected function get_blocks_variable_name() : string {
    return $this->blocks_variable_name;
  }

  /**
   * Get blocks custom folder full path.
   *
   * @return string
   *
   * @since 1.0.0
   */
  protected function get_blocks_custom_path() : string {
    return "{$this->get_blocks_path()}/custom";
  }

  /**
   * Get raw blocks manifest data.
   * Method is converted in json because there can be multiple blocks in array.
   * This data is stored in global variable as a string.
   * Not using array because php <= 7.0 doesn't support it.
   *
   * @throws Exception\Missing_Block_Name Throws error if block name is missing.
   *
   * @return string
   *
   * @since 1.0.0
   */
  protected function get_blocks_raw() : string {

    $namespace = $this->get_blocks_namespace();

    $blocks = array_map(
      function( $block ) use ( $namespace ) {

        $block = implode( ' ', file( ( $block ) ) );
        $block = json_decode( $block, true );

        if ( ! isset( $block['blockName'] ) ) {
          throw Missing_Block_Name::name_exception();
        }

        $block['namespace']     = $namespace;
        $block['blockFullName'] = "{$namespace}/{$this->get_block_name( $block )}";

        return $this->set_manifest_defaults( $block );
      },
      glob( "{$this->get_blocks_custom_path()}/*/manifest.json" )
    );

    return wp_json_encode( $blocks );
  }

  /**
   * Set All Manifest defaults so they can be missing from manifest.json if not used.
   *
   * @param array $block_details Block Manifest details.
   *
   * @return string
   *
   * @since 1.0.0
   */
  protected function set_manifest_defaults( array $block_details ) : array {
    if ( ! isset( $block_details['classes'] ) ) {
      $block_details['classes'] = [];
    }

    if ( ! isset( $block_details['attributes'] ) ) {
      $block_details['attributes'] = [];
    }

    if ( ! isset( $block_details['hasWrapper'] ) ) {
      $block_details['hasWrapper'] = true;
    }

    if ( ! isset( $block_details['hasInnerBlocks'] ) ) {
      $block_details['hasInnerBlocks'] = false;
    }

    return $block_details;
  }

  /**
   * Get blocks manifest data.
   * Output is converted into array.
   *
   * @return array
   *
   * @since 1.0.0
   */
  protected function get_blocks() : array {
    return json_decode( constant( $this->get_blocks_variable_name() ), true );
  }

  /**
   * Get block view path.
   *
   * @param string $block_name Block Name value.
   *
   * @return string
   *
   * @since 1.0.0
   */
  protected function get_block_view_path( string $block_name ) : string {
    return "{$this->get_blocks_custom_path()}/{$block_name}/{$block_name}.php";
  }
}
