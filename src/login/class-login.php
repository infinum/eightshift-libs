<?php
/**
 * The login page specific functionality.
 *
 * @package Eightshift_Libs\Login
 */

declare( strict_types=1 );

namespace Eightshift_Libs\Login;

use Eightshift_Libs\Core\Service;

/**
 * Class Login
 *
 * This class handles all login page options.
 *
 * @since 1.0.0
 */
class Login implements Service {

  /**
   * Register all the hooks
   *
   * @return void
   *
   * @since 1.0.0
   */
  public function register() {
    add_filter( 'login_headerurl', [ $this, 'custom_login_url' ] );
  }

  /**
   * Change default logo link with home url.
   *
   * @return string
   *
   * @since 1.0.0
   */
  public function custom_login_url() : string {
    return \home_url( '/' );
  }
}
