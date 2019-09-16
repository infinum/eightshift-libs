<?php
/**
 * Class Missing_Blocks_Manifest provides Exception if blocks global manifest.json is not foud.
 *
 * @since   1.0.0
 * @package Eightshift_Blocks\Exception
 */

namespace Eightshift_Blocks\Exception;

/**
 * Class Missing_Blocks_Manifest.
 *
 * @since 1.0.0
 */
class Missing_Blocks_Manifest extends \InvalidArgumentException implements General_Exception {

  /**
   * Create a new instance of the exception for an missing block name.
   *
   * @param string $blocks_manifest_path Blocks Manifest Path on disc.
   *
   * @return static
   *
   * @since 1.0.0
   */
  public static function manifest_exception( string $blocks_manifest_path ) {
    $message = sprintf(
      esc_html__( 'Missing global blocks manifest.json settings in location: %1$s. Check if manifest.json is in correct location.', 'eightshift-blocks' ),
      $blocks_manifest_path
    );

    return new static( $message );
  }
}
