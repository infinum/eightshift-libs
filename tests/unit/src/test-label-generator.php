<?php
/**
 * Label generator tests
 *
 * The tests that cover label generator class
 *
 * @since   0.9.0
 * @package Eightshift_Libs\Tests\Unit
 */

namespace Eightshift_Libs\Tests\Unit;

use Eightshift_Libs\Custom_Post_Type\Label_Generator;
use Eightshift_Libs\Exception\Invalid_Nouns;

/**
 * Test the custom post type functionality of the lib
 */
class Label_Generator_Functionality extends Init_Test_Case {

  /**
   * Test that label generator class returns correct labels
   */
  public function test_label_gnerator_works() {
    $nouns = [
      Label_Generator::SINGULAR_NAME_UC => esc_html_x( 'FAQ', 'post type upper case singular name', 'eightshift-libs' ),
      Label_Generator::SINGULAR_NAME_LC => esc_html_x( 'faq', 'post type lower case singular name', 'eightshift-libs' ),
      Label_Generator::PLURAL_NAME_UC   => esc_html_x( 'FAQs', 'post type upper case plural name', 'eightshift-libs' ),
      Label_Generator::PLURAL_NAME_LC   => esc_html_x( 'faqs', 'post type lower case plural name', 'eightshift-libs' ),
    ];

    $labels = ( new Label_Generator() )->get_generated_labels( $nouns );

    $this->assertIsArray( $labels );
    $this->assertArrayHasKey( 'name', $labels );
    $this->assertArrayHasKey( 'singular_name', $labels );
    $this->assertArrayHasKey( 'menu_name', $labels );
    $this->assertArrayHasKey( 'name_admin_bar', $labels );
    $this->assertArrayHasKey( 'archives', $labels );
    $this->assertArrayHasKey( 'attributes', $labels );
    $this->assertArrayHasKey( 'parent_item_colon', $labels );
    $this->assertArrayHasKey( 'all_items', $labels );
    $this->assertArrayHasKey( 'add_new_item', $labels );
    $this->assertArrayHasKey( 'add_new', $labels );
    $this->assertArrayHasKey( 'new_item', $labels );
    $this->assertArrayHasKey( 'edit_item', $labels );
    $this->assertArrayHasKey( 'update_item', $labels );
    $this->assertArrayHasKey( 'view_item', $labels );
    $this->assertArrayHasKey( 'view_items', $labels );
    $this->assertArrayHasKey( 'search_items', $labels );
    $this->assertArrayHasKey( 'not_found', $labels );
    $this->assertArrayHasKey( 'not_found_in_trash', $labels );
    $this->assertArrayHasKey( 'featured_image', $labels );
    $this->assertArrayHasKey( 'set_featured_image', $labels );
    $this->assertArrayHasKey( 'remove_featured_image', $labels );
    $this->assertArrayHasKey( 'use_featured_image', $labels );
    $this->assertArrayHasKey( 'insert_into_item', $labels );
    $this->assertArrayHasKey( 'uploaded_to_this_item', $labels );
    $this->assertArrayHasKey( 'items_list', $labels );
    $this->assertArrayHasKey( 'items_list_navigation', $labels );
    $this->assertArrayHasKey( 'filter_items_list', $labels );

  }

  /**
   * Test that label generator class throws exception
   */
  public function test_label_gnerator_throws_error() {

    $this->expectException( Invalid_Nouns::class );

    $nouns = [
      Label_Generator::SINGULAR_NAME_LC => esc_html_x( 'faq', 'post type lower case singular name', 'eightshift-libs' ),
      Label_Generator::PLURAL_NAME_UC   => esc_html_x( 'FAQs', 'post type upper case plural name', 'eightshift-libs' ),
      Label_Generator::PLURAL_NAME_LC   => esc_html_x( 'faqs', 'post type lower case plural name', 'eightshift-libs' ),
    ];

    $labels = ( new Label_Generator() )->get_generated_labels( $nouns );
  }
}
