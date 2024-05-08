<?php

/**
 * Modify WordPress admin behavior
 *
 * @package %g_namespace%\ModifyAdminAppearance
 */

declare(strict_types=1);

namespace %g_namespace%\ModifyAdminAppearance;

use %g_use_libs%\Services\ServiceInterface;

/**
 * Class that modifies some administrator appearance
 *
 * Example: Change color based on environment, remove dashboard widgets etc.
 */
class ModifyAdminAppearanceExample implements ServiceInterface
{
	/**
	 * List of admin color schemes.
	 *
	 * @var array<string, string>
	 */
	public const COLOR_SCHEMES = [
		'development' => 'fresh',
		'local' => 'fresh',
		'staging' => 'blue',
		'production' => 'sunrise',
	];

	/**
	 * Register all the hooks
	 *
	 * @return void
	 */
	public function register(): void
	{
		\add_filter('get_user_option_admin_color', [$this, 'adminColor'], 10, 0);
	}

	/**
	 * Method that changes admin colors based on environment variable
	 *
	 * @return string Modified color scheme..
	 */
	public function adminColor(): string
	{
		$env = \wp_get_environment_type();

		$colors = self::COLOR_SCHEMES;

		if (!isset($colors[$env])) {
			return $colors['development'];
		}

		return $colors[$env];
	}
}
