<?php
/**
 * Modify WordPress admin behavior
 *
 * @package EightshiftBoilerplate\Admin
 */

declare( strict_types=1 );

namespace EightshiftBoilerplate\Admin;

use EightshiftLibs\Admin\AbstractModifyAdminAppearance;

/**
 * Class that modifies some administrator appearance
 *
 * Example: Change color based on environment, remove dashboard widgets etc.
 */
class ModifyAdminAppearance extends AbstractModifyAdminAppearance {

  /**
   * Register all the hooks
   *
   * @return void
   */
  public function register() {
    \add_filter( 'get_user_option_admin_color', [ $this, 'set_admin_color' ], 10, 0 );
  }

  /**
   * Method that changes admin colors based on environment variable
   *
   * @return string Modified color scheme..
   */
  public function admin_color() : string {
    return $this->set_admin_color( EB_ENV );
  }

}
