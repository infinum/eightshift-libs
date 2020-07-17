<?php
/**
 * File containing invalid Gutenberg Block exceptions
 *
 * @package Eightshiftlibs\Exception
 */

 declare( strict_types=1 );

namespace Eightshiftlibs\Exception;

/**
 * Class Invalid_Block.
 *
 * @since 2.0.0
 */
final class FinalInvalidBlock extends \InvalidArgumentException implements GeneralExceptionInterface {

  /**
   * Throws error if blocks are missing.
   *
   * @return static
   *
   * @since 2.0.0
   */
  public static function missing_blocks_exception() {
    return new static( esc_html__( 'There are no blocks added in your project.', 'eightshift-libs' ) );
  }

  /**
   * Throws error if manifest key blockName is missing.
   *
   * @param string $block_path Full block path for the missing name.
   *
   * @return static
   *
   * @since 2.0.0
   */
  public static function missing_name_exception( string $block_path ) {
    return new static(
      sprintf(
        esc_html__( 'Block in this path %s is missing blockName key in its manifest.json.', 'eightshift-libs' ),
        $block_path
      )
    );
  }

  /**
   * Throws error if block view is missing.
   *
   * @param string $block_name Block name for the missing view.
   * @param string $block_path Full block path for the missing name.
   *
   * @return static
   *
   * @since 2.0.0
   */
  public static function missing_view_exception( string $block_name, string $block_path ) {
    return new static(
      sprintf(
        esc_html__( 'Block with this name %1$s is missing view template. Template name should be called %1$s.php, and it should be located in this path %2$s', 'eightshift-libs' ),
        $block_name,
        $block_path
      )
    );
  }

  /**
   * Throws error if render block view is missing.
   *
   * @param string $block_path Full block path for the missing name.
   *
   * @return static
   *
   * @since 2.0.0
   */
  public static function missing_render_view_exception( string $block_path ) {
    return new static(
      sprintf(
        esc_html__( 'Block view is missing in the provided path. Please chech if %s is the right path for your block view.', 'eightshift-libs' ),
        $block_path
      )
    );
  }

  /**
   * Throws error if global settings manifest.json is missing.
   *
   * @param string $settings_manifest_path Full path for the missing manifest.json.
   *
   * @return static
   *
   * @since 2.0.0
   */
  public static function missing_settings_manifest_exception( string $settings_manifest_path ) {
    return new static(
      sprintf(
        esc_html__( 'Global blocks settings manifest.json is missing on this location: %s.', 'eightshift-libs' ),
        $settings_manifest_path
      )
    );
  }

  /**
   * Throws error if wrapper settings manifest.json is missing.
   *
   * @param string $settings_manifest_path Full path for the missing manifest.json.
   *
   * @return static
   *
   * @since 2.0.0
   */
  public static function missing_wrapper_manifest_exception( string $settings_manifest_path ) {
    return new static(
      sprintf(
        esc_html__( 'Wrapper blocks settings manifest.json is missing on this location: %s.', 'eightshift-libs' ),
        $settings_manifest_path
      )
    );
  }

  /**
   * Throws error if block wrapper view is missing.
   *
   * @param string $wrapper_path Full wrapper path.
   *
   * @return static
   *
   * @since 2.0.0
   */
  public static function missing_wrapper_view_exception( string $wrapper_path ) {
    return new static(
      sprintf(
        esc_html__( 'Wrapper view is missing. Template should be located in this path %s', 'eightshift-libs' ),
        $wrapper_path
      )
    );
  }

  /**
   * Throws error if global manifest settings key namespace is missing.
   *
   * @return static
   *
   * @since 2.0.0
   */
  public static function missing_namespace_exception() {
    return new static(
      esc_html__( 'Global Blocks settings manifest.json is missing a key called namespace. This key prefixes all block names.', 'eightshift-libs' )
    );
  }
}
