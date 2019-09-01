<?php
/**
 * Main plugin fixture
 *
 * This class will mock the behavior of the main plugin class that
 * extends the main lib class which is used to kickstart all the depenedncies.
 *
 * @since   0.9.0
 * @package Eightshift_Libs\Tests\Fixtures\Plugin
 */

namespace Eightshift_Libs\Tests\Fixtures\Plugin;

use Eightshift_Libs\Core\Main;

class Plugin_Entrypoint extends Main {

  const DEFAULT_REGISTER_ACTION_HOOK = 'plugins_loaded';

  protected function get_service_classes() : array {
    return [
      Dependency::class,
      Rest::class => [ Dependency::class ],
    ];
  }
}
