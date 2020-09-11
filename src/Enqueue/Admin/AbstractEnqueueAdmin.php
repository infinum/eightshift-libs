<?php
/**
 * The Admin Enqueue specific functionality.
 *
 * @package EightshiftLibs\Enqueue\Admin
 */

declare( strict_types=1 );

namespace EightshiftLibs\Enqueue\Admin;

use EightshiftLibs\Enqueue\AbstractAssets;
use EightshiftLibs\Manifest\ManifestInterface;

/**
 * Class EnqueueAdmin
 *
 * This class handles enqueue scripts and styles.
 */
abstract class AbstractEnqueueAdmin extends AbstractAssets {

  const ADMIN_SCRIPT_URI = 'applicationAdmin.js';
  const ADMIN_STYLE_URI  = 'applicationAdmin.css';

  /**
   * Instance variable of manifest data.
   *
   * @var ManifestInterface
   */
  protected $manifest;

  /**
   * Register all the hooks
   *
   * @return void
   */
  public function register() : void {
    add_action( 'login_enqueue_scripts', [ $this, 'enqueue_styles' ] );
    add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_styles' ], 50 );
    add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
  }

  /**
   * Register the Stylesheets for the admin area.
   *
   * @return void
   */
  public function enqueue_styles() : void {
    $handle = "{$this->get_assets_prefix()}-styles";

    \wp_register_style(
      $handle,
      $this->manifest->get_assets_manifest_item( static::ADMIN_STYLE_URI ),
      $this->get_admin_style_dependencies(),
      $this->get_assets_version(),
      $this->get_media()
    );

    \wp_enqueue_style( $handle );

  }

  /**
   * Register the JavaScript for the admin area.
   *
   * @return void
   */
  public function enqueue_scripts() : void {
    $handle = "{$this->get_assets_prefix()}-scripts";

    \wp_register_script(
      $handle,
      $this->manifest->get_assets_manifest_item( static::ADMIN_SCRIPT_URI ),
      $this->get_admin_script_dependencies(),
      $this->get_assets_version(),
      $this->script_in_footer()
    );

    \wp_enqueue_script( $handle );

    foreach ( $this->get_localizations() as $object_name => $data_array ) {
      \wp_localize_script( $handle, $object_name, $data_array );
    }
  }
}
