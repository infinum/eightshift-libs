<?php
/**
 * Custom taxonomy tests
 *
 * The tests that cover abstract class used to create a custom taxonomy.
 *
 * @since   0.9.0
 * @package Eightshift_Libs\Tests\Unit
 */

namespace Eightshift_Libs\Tests\Unit;

use Eightshift_Libs\Tests\Fixtures\Plugin\Faq_Taxonomy;

use \Brain\Monkey\Functions;

/**
 * Test the custom taxonomy functionality of the lib
 */
class Custom_Taxonomy extends Init_Test_Case {

  /**
   * Test that hook is called
   */
  public function test_register_hook_correct() {
    ( new Faq_Taxonomy() )->register();

    $this->assertTrue( has_action( 'init', 'Eightshift_Libs\Tests\Fixtures\Plugin\Faq_Taxonomy->register_custom_taxonomy()' ) );

  }

  /**
   * Test that the callback function inside register_custom_taxonomy will be called once
   */
  public function test_callback_in_hook_is_called() {
    Functions\when( 'register_taxonomy' )->justReturn( true );

    $test_callback = ( new Faq_Taxonomy() )->register_custom_taxonomy();

    $this->assertEmpty( $test_callback );
  }
}
