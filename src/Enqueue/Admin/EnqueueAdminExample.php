<?php

/**
 * The Admin Enqueue specific functionality.
 *
 * @package %namespace%\Enqueue\Admin
 */

declare(strict_types=1);

namespace %namespace%\Enqueue\Admin;

use %namespace%\Config\Config;
use %useLibs%\Enqueue\Admin\AbstractEnqueueAdmin;

/**
 * Class EnqueueAdminExample
 *
 * This class handles enqueue scripts and styles.
 */
class EnqueueAdminExample extends AbstractEnqueueAdmin
{
	/**
	 * Register all the hooks
	 *
	 * @return void
	 */
	public function register(): void
	{
		\add_action('login_enqueue_scripts', [$this, 'enqueueStyles']);
		\add_action('admin_enqueue_scripts', [$this, 'enqueueStyles'], 50);
		\add_action('admin_enqueue_scripts', [$this, 'enqueueScripts']);
	}

	/**
	 * Method that returns assets name used to prefix asset handlers.
	 *
	 * @return string
	 */
	public function getAssetsPrefix(): string
	{
		return Config::getProjectName();
	}

	/**
	 * Method that returns assets version for versioning asset handlers.
	 *
	 * @return string
	 */
	public function getAssetsVersion(): string
	{
		return Config::getProjectVersion();
	}
}
