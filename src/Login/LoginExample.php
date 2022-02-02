<?php

/**
 * The login page specific functionality.
 *
 * @package EightshiftBoilerplate\Login
 */

declare(strict_types=1);

namespace EightshiftBoilerplate\Login;

use EightshiftLibs\Services\ServiceInterface;

/**
 * Class Login
 *
 * This class handles all login page options.
 */
class LoginExample implements ServiceInterface
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

	/**
	 * Change default logo link with home url.
	 *
	 * @return string Modified login header logo URL.
	 */
	public function customLoginUrl(): string
	{
		return \home_url('/');
	}
}
