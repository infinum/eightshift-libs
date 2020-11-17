<?php

/**
 * Modify WordPress admin behavior
 *
 * @package EightshiftBoilerplate\ModifyAdminAppearance
 */

declare(strict_types=1);

namespace EightshiftBoilerplate\ModifyAdminAppearance;

use EightshiftLibs\ModifyAdminAppearance\AbstractModifyAdminAppearance;

/**
 * Class that modifies some administrator appearance
 *
 * Example: Change color based on environment, remove dashboard widgets etc.
 */
class ModifyAdminAppearanceExample extends AbstractModifyAdminAppearance
{

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
		return $this->setAdminColor(defined('EB_ENV') ? EB_ENV : 'default');
	}
}
