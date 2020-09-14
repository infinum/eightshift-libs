<?php
/**
 * Modify WordPress admin behavior
 *
 * @package EightshiftLibs\Admin
 */

declare( strict_types=1 );

namespace EightshiftLibs\Admin;

use EightshiftLibs\Services\ServiceInterface;

/**
 * Class that modifies some administrator appearance
 *
 * Example: Change color based on environment, remove dashboard widgets etc.
 */
abstract class AbstractModifyAdminAppearance implements ServiceInterface {

  /**
   * List of admin color schemes.
   *
   * @var array
   */
  const COLOR_SCHEMES = [
    'default'    => 'fresh',
    'staging'    => 'blue',
    'production' => 'sunrise',
  ];

  /**
   * List of admin color schemes.
   *
   * @var array
   */
  public function get_color_schemes() : array {
    return self::COLOR_SCHEMES;
  }

  /**
   * Method that changes admin colors based on environment variable
   *
   * @param string $env Env state.
   *
   * @return string Modified color scheme.
   */
  public function set_admin_color( string $env ) : string {
    $colors = $this->get_color_schemes();

    if ( ! isset( $colors[ $env ] ) ) {
      return $colors['default'];
    }

    return $colors[ $env ];
  }
}
