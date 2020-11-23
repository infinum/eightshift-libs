<?php

/**
 * The login page specific functionality.
 *
 * @package EightshiftBoilerplate\Login
 */

declare(strict_types=1);

namespace EightshiftBoilerplate\Login;

use EightshiftLibs\Login\AbstractLogin;

/**
 * Class Login
 *
 * This class handles all login page options.
 */
class LoginExample extends AbstractLogin
{

	/**
	 * Register all the hooks
	 *
	 * @return void
	 */
	public function register(): void
	{
		\add_filter('login_headerurl', [$this, 'customLoginUrl']);
	}
}
