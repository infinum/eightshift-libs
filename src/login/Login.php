<?php
/**
 * The login page specific functionality.
 *
 * @package EightshiftLibs\Login
 */

declare( strict_types=1 );

namespace EightshiftLibs\Login;

use EightshiftLibs\Core\ServiceInterface;

/**
 * Class Login
 *
 * This class handles all login page options.
 */
class Login implements ServiceInterface {

  /**
   * Register all the hooks
   *
   * @return void
   */
  public function register() {
    add_filter( 'login_headerurl', [ $this, 'custom_login_url' ] );
  }

  /**
   * Change default logo link with home url.
   *
   * @return string
   */
  public function custom_login_url() : string {
    return \home_url( '/' );
  }
}
