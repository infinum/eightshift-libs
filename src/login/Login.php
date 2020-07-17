<?php
/**
 * The login page specific functionality.
 *
 * @package Eightshiftlibs\Login
 */

declare( strict_types=1 );

namespace Eightshiftlibs\Login;

use Eightshiftlibs\Core\ServiceInterface;

/**
 * Class Login
 *
 * This class handles all login page options.
 *
 * @since 1.0.0
 */
class Login implements ServiceInterface {

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
