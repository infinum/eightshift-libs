<?php
/**
 * Class Blocks_Full_Data holds abstract class for Gutenberg blocks data.
 *
 * @since   1.0.0
 * @package Eightshift_Blocks
 */

namespace Eightshift_Blocks;

use Eightshift_Libs\Core\Service;
use Eightshift_Blocks\Blocks_Settings_Data;
use Eightshift_Blocks\Blocks_Data;
use Eightshift_Blocks\Wrapper_Data;

/**
 * Class Blocks_Full_Data
 *
 * @since 1.0.0
 */
abstract class Blocks_Full_Data implements Service {

  /**
   * Trait Blocks_Settings_Data holds blocks global settings manifest data.
   *
   * @var object
   *
   * @since 1.0.0
   */
  use Blocks_Settings_Data;

  /**
   * Trait Blocks_Data holds blocks manifest data.
   *
   * @var object
   *
   * @since 1.0.0
   */
  use Blocks_Data;

  /**
   * Trait Wrapper_Data holds wrapper manifest data.
   *
   * @var object
   *
   * @since 1.0.0
   */
  use Wrapper_Data;

  /**
   * Register all the hooks
   *
   * @since 1.0.0
   */
  public function register() {

    // Set blocks settings global variable.
    add_action( 'init', [ $this, 'register_blocks_settings_variable' ] );

    // Set wrapper global variable.
    add_action( 'init', [ $this, 'register_wrapper_variable' ] );

    // Set all blocks global variable.
    add_action( 'init', [ $this, 'register_blocks_variable' ] );
  }

  /**
   * Get Projects Theme path.
   * If you are using a plugin, override method must be provided with correct blocks folder.
   *
   * @return string
   *
   * @since 1.0.7 Removing static method.
   * @since 1.0.0
   */
  public function get_blocks_path() : string {
    return get_template_directory() . '/src/blocks';
  }

  /**
   * Get all blocks with full block name used to limit what blocks are going to be used in the project.
   *
   * @return array
   *
   * @since 1.0.0
   */
  public function get_all_blocks_list() : array {
    return array_map(
      function( $block ) {
        return $block['blockFullName'];
      },
      $this->get_blocks()
    );
  }

  /**
   * Get blocks name value.
   *
   * @param array $block_details Block Manifest details.
   *
   * @return string
   *
   * @since 1.0.0
   */
  protected function get_block_name( array $block_details ) : string {
    return $block_details['blockName'];
  }

  /**
   * Get blocks fullname value.
   *
   * @param array $block_details Block Manifest details.
   *
   * @return string
   *
   * @since 1.0.0
   */
  protected function get_block_full_name( array $block_details ) : string {
    return $block_details['blockFullName'];
  }

  /**
   * Get blocks namespace value from blocks global settings.
   *
   * @return string
   *
   * @since 1.0.0
   */
  protected function get_blocks_namespace() : string {
    $settings = $this->get_blocks_settings();

    return $settings['namespace'];
  }
}
