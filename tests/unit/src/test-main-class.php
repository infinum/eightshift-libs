<?php
/**
 * Main lib class tests
 *
 * Here are the tests for all the main functionality of the library
 *
 * @since   0.9.0
 * @package Eightshift_Libs\Tests\Unit
 */

namespace Eightshift_Libs\Tests\Unit;

use Eightshift_Libs\Tests\Fixtures\Plugin\Plugin_Entrypoint;
use Eightshift_Libs\Tests\Fixtures\Theme_Entrypoint;

/**
 * Test the main functionality of the lib
 */
class Main_Functionality extends Init_Test_Case {

  /**
   * Test that the main lib entrypoint action is registered for plugins
   */
  public function test_main_register_action_is_added_in_plugins() {
    // Setup fake plugin entrypoint.
    ( new Plugin_Entrypoint() )->register();

    // The action hook needs to be different than the default 'after_setup_theme'.
    $this->assertTrue( has_action( 'plugins_loaded', 'Eightshift_Libs\Tests\Fixtures\Plugin\Plugin_Entrypoint->register_services()' ) );
  }

  /**
   * Test that the main lib entrypoint action is registered for themes
   */
  public function test_main_register_action_is_added_in_themes() {
    // Setup fake theme entrypoint.
    ( new Theme_Entrypoint() )->register();

    $this->assertTrue( has_action( 'after_setup_theme', 'Eightshift_Libs\Tests\Fixtures\Theme_Entrypoint->register_services()' ) );
  }

  /**
   * Test that nothing is returned and that the method returns empty services
   */
  public function test_empty_services() {
    $services = ( new Theme_Entrypoint() )->register_services();

    $this->assertEmpty( $services );
  }

  /**
   * Test that services will be loaded in the case of servicese
   * that have and don't have a register method.
   */
  public function test_services_are_registered_and_loaded() {
    $services = ( new Plugin_Entrypoint() )->register_services();

    $this->assertEmpty( $services );
  }
}
