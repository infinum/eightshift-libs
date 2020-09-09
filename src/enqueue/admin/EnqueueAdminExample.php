<?php
/**
 * The Admin Enqueue specific functionality.
 *
 * @package EightshiftLibs\Enqueue\Admin
 */

declare( strict_types=1 );

namespace EightshiftLibs\Enqueue\Admin;

use EightshiftBoilerplate\Config\Config;
use EightshiftLibs\Enqueue\Admin\AbstractEnqueueAdmin;
use EightshiftLibs\Manifest\ManifestInterface;

/**
 * Class EnqueueAdminExample
 *
 * This class handles enqueue scripts and styles.
 */
class EnqueueAdminExample extends AbstractEnqueueAdmin {

  /**
   * Create a new admin instance.
   *
   * @param ManifestInterface $manifest Inject manifest which holds data about assets from manifest.json.
   */
  public function __construct( ManifestInterface $manifest ) {
    $this->manifest = $manifest;
  }

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
   * Method that returns assets name used to prefix asset handlers.
   *
   * @return string
   */
  public function get_assets_prefix() : string {
    return Config::get_project_name();
  }

  /**
   * Method that returns assets version for versioning asset handlers.
   *
   * @return string
   */
  public function get_assets_version() : string {
    return Config::get_project_version();
  }
}
