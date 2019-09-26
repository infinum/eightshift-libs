<?php
/**
 * The Admin Enqueue specific functionality.
 *
 * @package Eightshift_Libs\Enqueue
 */

declare( strict_types=1 );

namespace Eightshift_Libs\Enqueue;

use Eightshift_Libs\Core\Service;
use Eightshift_Libs\Manifest\Manifest_Data;
use Eightshift_Libs\Core\Config_Data;

/**
 * Class Enqueue_Admin
 *
 * This class handles enqueue scripts and styles.
 *
 * @since 2.0.0
 */
class Enqueue_Admin implements Service {

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
   * @since 2.0.0
   */
  protected $manifest;

  /**
   * Create a new admin instance.
   *
   * @param Config_Data   $config Inject config which holds data regarding project details.
   * @param Manifest_Data $manifest Inject manifest which holds data about assets from manifest.json.
   *
   * @since 2.0.0 Adding Config as a new DI.
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
   * @since 2.0.0
   */
  public function register() {
    add_action( 'login_enqueue_scripts', [ $this, 'enqueue_styles' ] );
    add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_styles' ], 50 );
    add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
  }

  /**
   * Register the Stylesheets for the admin area.
   *
   * @return void
   *
   * @since 2.0.0
   */
  public function enqueue_styles() {
    $handler = "{$this->config->get_project_prefix()}-styles";

    \wp_register_style(
      $handler,
      $this->manifest->get_assets_manifest_item( 'applicationAdmin.css' ),
      [],
      $this->config->get_project_version()
    );
    \wp_enqueue_style( $handler );

  }

  /**
   * Register the JavaScript for the admin area.
   *
   * @return void
   *
   * @since 2.0.0
   */
  public function enqueue_scripts() {
    $handler = "{$this->config->get_project_prefix()}-scripts";

    \wp_register_script(
      $handler,
      $this->manifest->get_assets_manifest_item( 'applicationAdmin.js' ),
      [],
      $this->config->get_project_version(),
      true
    );
    \wp_enqueue_script( $handler );
  }
}
