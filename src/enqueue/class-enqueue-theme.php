<?php
/**
 * The Theme/Frontend Enqueue specific functionality.
 *
 * @package Eightshift_Libs\Enqueue
 */

declare( strict_types=1 );

namespace Eightshift_Libs\Enqueue;

use Eightshift_Libs\Core\Service;
use Eightshift_Libs\Manifest\Manifest_Data;
use Eightshift_Libs\Core\Config_Data;

/**
 * Class Enqueue
 *
 * @since 2.0.0
 */
class Enqueue_Theme extends Assets {

  const IN_FOOTER = true;

  const THEME_SCRIPT_URI = 'application.js';
  const THEME_STYLE_URI  = 'application.css';

  /**
   * Instance variable of project config data.
   *
   * @var object
   *
   * @since 2.0.0
   */
  protected $config;

  /**
   * Instance variable of manifest data.
   *
   * @var object
   *
   * @since 2.0.0
   */
  protected $manifest;

  /**
   * Create a new admin instance.
   *
   * @param Config_Data   $config Inject config which holds data regarding project details.
   * @param Manifest_Data $manifest Inject manifest which holds data about assets from manifest.json.
   *
   * @since 2.0.0
   */
  public function __construct( Config_Data $config, Manifest_Data $manifest ) {
    $this->config   = $config;
    $this->manifest = $manifest;
  }

  /**
   * Register all the hooks
   *
   * @return void
   *
   * @since 2.0.0
   */
  public function register() {
    add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ], 10 );
    add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
  }

  /**
   * Register the Stylesheets for the front end of the theme.
   *
   * @return void
   *
   * @since 2.0.0
   * @since 2.0.3 Added methods for overrides.
   *              Fixed static calls from config class.
   */
  public function enqueue_styles() {
    $handle = "{$this->config::get_project_prefix()}-theme-styles";

    \wp_register_style(
      $handle,
      $this->manifest->get_assets_manifest_item( static::THEME_STYLE_URI ),
      $this->get_frontend_style_dependencies(),
      $this->config::get_project_version(),
      $this->get_media()
    );

    \wp_enqueue_style( $handle );
  }

  /**
   * Register the JavaScript for the front end of the theme.
   *
   * @return void
   *
   * @since 2.0.0
   * @since 2.0.3 Added methods for overrides.
   *              Fixed static calls from config class.
   */
  public function enqueue_scripts() {
    $handle = "{$this->config::get_project_prefix()}-scripts";

    \wp_register_script(
      $handle,
      $this->manifest->get_assets_manifest_item( static::THEME_SCRIPT_URI ),
      $this->get_frontend_script_dependencies(),
      $this->config::get_project_version(),
      $this->script_in_footer()
    );

    \wp_enqueue_script( $handle );

    foreach ( $this->get_localizations() as $object_name => $data_array ) {
      \wp_localize_script( $handle, $object_name, $data_array );
    }
  }
}
