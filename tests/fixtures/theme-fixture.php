<?php
/**
 * Main theme fixture
 *
 * This class will mock the behavior of the main theme class that
 * extends the main lib class which is used to kickstart all the depenedncies.
 *
 * @since   0.9.0
 * @package Eightshift_Libs\Tests\Fixtures
 */

namespace Eightshift_Libs\Tests\Fixtures;

use Eightshift_Libs\Tests\Fixtures\Theme\Rest;
use Eightshift_Libs\Tests\Fixtures\Theme\Dependency;

use Eightshift_Libs\Core\Main;

class Theme_Entrypoint extends Main {
  protected function get_service_classes() : array {
    return [];
  }
}
