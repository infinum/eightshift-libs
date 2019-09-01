<?php
/**
 * Custom post type tests
 *
 * The tests that cover abstract class used to create a custom post type.
 *
 * @since   0.9.0
 * @package Eightshift_Libs\Tests\Unit
 */

namespace Eightshift_Libs\Tests\Unit;

use Eightshift_Libs\Tests\Fixtures\Plugin\Faq_Post_Type;

use \Brain\Monkey\Functions;

/**
 * Test the custom post type functionality of the lib
 */
class Custom_Post_Type extends Init_Test_Case {

  /**
   * Test that hook is called
   */
  public function test_register_hook_correct() {
    ( new Faq_Post_Type() )->register();

    $this->assertTrue( has_action( 'init', 'Eightshift_Libs\Tests\Fixtures\Plugin\Faq_Post_Type->register_post_type()' ) );

  }

  /**
   * Test that the callback function inside register_post_type will be called once
   */
  public function test_callback_in_hook_is_called() {
    Functions\when( 'register_post_type' )->justReturn( true );

    $test_callback = ( new Faq_Post_Type() )->register_post_type();

    $this->assertEmpty( $test_callback );
  }
}
