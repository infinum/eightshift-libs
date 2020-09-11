<?php
/**
 * The Theme/Frontend Enqueue specific functionality.
 *
 * @package EightshiftLibs\Enqueue\Theme
 */

declare( strict_types=1 );

namespace EightshiftLibs\Enqueue\Theme;

use EightshiftLibs\Enqueue\AbstractAssets;
use EightshiftLibs\Manifest\ManifestInterface;

/**
 * Class Enqueue
 */
abstract class AbstractEnqueueTheme extends AbstractAssets {

  const THEME_SCRIPT_URI = 'application.js';
  const THEME_STYLE_URI  = 'application.css';

  /**
   * Instance variable of manifest data.
   *
   * @var ManifestInterface
   */
  protected $manifest;

  /**
   * Register the Stylesheets for the front end of the theme.
   *
   * @return void
   */
  public function enqueue_styles() : void {
    $handle = "{$this->get_assets_prefix()}-theme-styles";

    \wp_register_style(
      $handle,
      $this->manifest->get_assets_manifest_item( static::THEME_STYLE_URI ),
      $this->get_frontend_style_dependencies(),
      $this->get_assets_version(),
      $this->get_media()
    );

    \wp_enqueue_style( $handle );
  }

  /**
   * Register the JavaScript for the front end of the theme.
   *
   * @return void
   */
  public function enqueue_scripts() : void {
    $handle = "{$this->get_assets_prefix()}-scripts";

    \wp_register_script(
      $handle,
      $this->manifest->get_assets_manifest_item( static::THEME_SCRIPT_URI ),
      $this->get_frontend_script_dependencies(),
      $this->get_assets_version(),
      $this->script_in_footer()
    );

    \wp_enqueue_script( $handle );

    foreach ( $this->get_localizations() as $object_name => $data_array ) {
      \wp_localize_script( $handle, $object_name, $data_array );
    }
  }
}
