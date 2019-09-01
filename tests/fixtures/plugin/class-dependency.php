<?php
/**
 * Fake test file with no hookable methods and register method.
 *
 * This is an example of a test class that can be used as a dependency for other classes.
 *
 * @since   0.9.0
 * @package Eightshift_Libs\Tests\Fixtures\Plugin
 */

namespace Eightshift_Libs\Tests\Fixtures\Plugin;

class Dependency implements Data {
  public function get_data() : string {
    return 'Some data';
  }
}
