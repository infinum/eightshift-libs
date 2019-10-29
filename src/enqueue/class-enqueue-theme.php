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
class Enqueue_Theme implements Service {

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
   * Register the Stylesheets for the theme area.
   *
   * @return void
   *
   * @since 2.0.0
   */
  public function enqueue_styles() {
    $handler = "{$this->config->get_project_prefix()}-theme-styles";

    \wp_register_style(
      $handler,
      $this->manifest->get_assets_manifest_item( 'application.css' ),
      [],
      $this->config->get_project_version()
    );
    \wp_enqueue_style( $handler );
  }

  /**
   * Register the JavaScript for the theme area.
   *
   * @return void
   *
   * @since 2.0.0
   */
  public function enqueue_scripts() {
    $handler = "{$this->config->get_project_prefix()}-scripts";

    \wp_register_script(
      $handler,
      $this->manifest->get_assets_manifest_item( 'application.js' ),
      [],
      $this->config->get_project_version(),
      true
    );
    \wp_enqueue_script( $handler );

    // Global variables for ajax and translations.
    \wp_localize_script(
      $handler,
      'projectLocalization',
      [
        'ajaxurl' => \admin_url( 'admin-ajax.php' ),
      ]
    );
  }
}
