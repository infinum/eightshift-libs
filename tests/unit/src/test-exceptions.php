<?php
/**
 * Exceptions tests
 *
 * Here are the tests for all the exceptions in the library
 *
 * @since   0.9.0
 * @package Eightshift_Libs\Tests\Unit
 */

namespace Eightshift_Libs\Tests\Unit;

use Eightshift_Libs\Exception\Failed_To_Load_View;
use Eightshift_Libs\Exception\Invalid_Callback;
use Eightshift_Libs\Exception\Invalid_Nouns;
use Eightshift_Libs\Exception\Invalid_Service;
use Eightshift_Libs\Exception\Missing_Block;
use Eightshift_Libs\Exception\Missing_Manifest;

use Eightshift_Libs\Tests\Fixtures\Plugin\Dependency;

/**
 * Test the exceptions in the library
 */
class Library_Exceptions extends Init_Test_Case {

  private $dummy_class;

  protected function setUp() {
    parent::setUp();

    $this->dummy_class = new Dependency();
  }

  /**
   * Test the exception message of the view load exception
   */
  public function test_view_load_failure_exception_message_correct() {
    $exception = new \Exception( 'Missing template' );
    $exception_object = Failed_To_Load_View::view_exception( 'view/template.php', $exception );

    $this->assertEquals( 'Could not load the View URI: view/template.php. Reason: Missing template.', $exception_object->getMessage() );
  }

  /**
   * Test the exception message of the invalid callback exception in the case that the callback is a string
   */
  public function test_invalid_callback_exception_message_correct_for_string() {
    $exception_object = Invalid_Callback::from_callback( 'get_view' );

    $this->assertEquals( 'The callback get_view is not recognized and cannot be registered.', $exception_object->getMessage() );
  }

  /**
   * Test the exception message of the invalid callback exception in the case that the callback is an object
   */
  public function test_invalid_callback_exception_message_correct_for_object() {
    $exception_object = Invalid_Callback::from_callback( $this->dummy_class );

    $this->assertEquals( 'The callback Eightshift_Libs\Tests\Fixtures\Plugin\Dependency is not recognized and cannot be registered.', $exception_object->getMessage() );
  }

  /**
   * Test the exception message of the invalid nouns exception
   */
  public function test_invalid_nouns_exception_message_correct() {
    $exception_object = Invalid_Nouns::from_key( 'singular_name_lc' );

    $this->assertEquals( 'The array of nouns passed into the Label_Generator is missing the singular_name_lc noun.', $exception_object->getMessage() );
  }

  /**
   * Test the exception message of the invalid service exception in the case that the service is a string
   */
  public function test_invalid_service_exception_message_correct() {
    $exception_object = Invalid_Service::from_service( 'Register_Plugin' );

    $this->assertEquals( 'The service Register_Plugin is not recognized and cannot be registered.', $exception_object->getMessage() );
  }

  /**
   * Test the exception message of the invalid service exception in the case that the service is an object
   */
  public function test_invalid_service_exception_message_correct_from_object() {
    $exception_object = Invalid_Service::from_service( $this->dummy_class );

    $this->assertEquals( 'The service Eightshift_Libs\Tests\Fixtures\Plugin\Dependency is not recognized and cannot be registered.', $exception_object->getMessage() );
  }

  /**
   * Test the exception message of the block exception and view exception for blocks
   */
  public function test_missing_block_exception_message_correct() {
    $exception_object_block = Missing_Block::name_exception();
    $exception_object_view  = Missing_Block::view_exception( 'paragraph', 'paragraph/index.php' );

    $this->assertEquals( 'Missing Block Name', $exception_object_block->getMessage() );
    $this->assertEquals( 'Missing view template for block called: paragraph | Expecting a template in path: paragraph/index.php', $exception_object_view->getMessage() );
  }

  /**
   * Test the exception message of the missing manifest exception
   */
  public function test_missing_manifest_exception_message_correct() {
    $exception_object = Missing_Manifest::message( 'Missing manifest' );

    $this->assertEquals( 'Missing manifest', $exception_object->getMessage() );
  }
}
