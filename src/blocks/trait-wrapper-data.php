<?php
/**
 * Class Wrapper_Data holds registration of global variable for cache and storing wrapper manifest data.
 *
 * @since   1.0.0
 * @package Eightshift_Blocks
 */

namespace Eightshift_Blocks;

/**
 * Class Wrapper_Data
 *
 * @since 1.0.0
 */
trait Wrapper_Data {

  /**
   * Default projects wrapper global variable name.
   * Used to store wrapper data.
   *
   * @var string
   *
   * @since 1.0.0
   */
  protected $wrapper_variable_name = 'ES_WRAPPER';

  /**
   * Define global variable for wrapper settings.
   * Used to cache data inside a global variable so you don't have to fetch manifest.json file on every call.
   *
   * @return void
   *
   * @since 1.0.0
   */
  public function register_wrapper_variable() {
    define( $this->get_wrapper_variable_name(), $this->get_wrapper_raw() );
  }

  /**
   * Get wrapper settings variable name to store the cached data into.
   *
   * @return string
   *
   * @since 1.0.0
   */
  protected function get_wrapper_variable_name() : string {
    return $this->wrapper_variable_name;
  }

  /**
   * Get wrapper folder full path.
   *
   * @return string
   *
   * @since 1.0.0
   */
  protected function get_wrapper_path() : string {
    return "{$this->get_blocks_path()}/wrapper";
  }

  /**
   * Get raw wrapper settings manifest data.
   * This data is stored in global variable as a string.
   * Not using array because php <= 7.0 doesn't support it.
   *
   * @return string
   *
   * @since 1.0.0
   */
  protected function get_wrapper_raw() : string {
    $manifest_path = "{$this->get_wrapper_path()}/manifest.json";

    if ( ! file_exists( implode( ' ', file( $manifest_path ) ) ) ) {
      return implode( ' ', file( $manifest_path ) );
    }
  }

  /**
   * Get wrapper manifest data.
   * Output is converted into array.
   *
   * @return array
   *
   * @since 1.0.0
   */
  protected function get_wrapper() : array {
    return json_decode( constant( $this->get_wrapper_variable_name() ), true );
  }

  /**
   * Get block wrapper view path.
   *
   * @return string
   *
   * @since 1.0.0
   */
  protected function get_block_wrapper_view_path() {
    return "{$this->get_wrapper_path()}/wrapper.php";
  }
}
