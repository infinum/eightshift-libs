<?php
/**
 * The login page specific functionality.
 *
 * @package EightshiftLibs\Login
 */

declare( strict_types=1 );

namespace EightshiftLibs\Login;

use EightshiftLibs\Login\AbstractLogin;

/**
 * Class Login
 *
 * This class handles all login page options.
 */
class LoginExample extends AbstractLogin {

  /**
   * Register all the hooks
   *
   * @return void
   */
  public function register() {
    \add_filter( 'login_headerurl', [ $this, 'custom_login_url' ] );
  }
}
