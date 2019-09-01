<?php
/**
 * Asset manifest tests
 *
 * The tests that cover Manifest class
 *
 * @since   0.9.0
 * @package Eightshift_Libs\Tests\Unit
 */

namespace Eightshift_Libs\Tests\Unit;

use Eightshift_Libs\Tests\Fixtures\Plugin\Manifest;
use Eightshift_Libs\Exception\Missing_Manifest;

use \Brain\Monkey\Functions;

/**
 * Test the custom post type functionality of the lib
 */
class Asset_Manifest extends Init_Test_Case {

  const SCRIPT = 'script019.js';

  protected function setUp() {
    parent::setUp();

    Functions\when( '\get_template_directory' )->justReturn( dirname( __FILE__, 3 ) . '/fixtures/plugin' );
    Functions\when( '\home_url' )->justReturn( 'https://www.example.org' );
  }

  public function test_global_variable_hook_is_added() {
    ( new Manifest() )->register();

    $this->assertTrue( has_action( 'init', 'Eightshift_Libs\Tests\Fixtures\Plugin\Manifest->register_global_variable()' ) );
  }

  public function test_global_variable_name_is_defined() {
    ( new Manifest() )->register_global_variable();

    $this->assertTrue( defined( 'ES_ASSETS_MANIFEST' ) );
    $this->assertEquals( 'string', gettype( ES_ASSETS_MANIFEST ) );
  }

  /**
   * @runInSeparateProcess
   * @preserveGlobalState disabled
   */
  public function test_global_missing_manifest() {
    Functions\when( '\get_template_directory' )->justReturn( '' );

    $this->expectException( Missing_Manifest::class );

    ( new Manifest() )->register_global_variable();
  }

  public function test_get_assets_manifest_no_key_defined() {
    // Needed so that GLOBAL_VARIABLE_NAME is defined.
    ( new Manifest() )->register_global_variable();

    $missing_asset = ( new Manifest() )->get_assets_manifest_item( '' );

    $this->assertEmpty( $missing_asset );
  }

  public function test_get_assets_manifest_works() {
    // Needed so that GLOBAL_VARIABLE_NAME is defined.
    ( new Manifest() )->register_global_variable();

    $asset_exists = ( new Manifest() )->get_assets_manifest_item( self::SCRIPT );

    $this->assertEquals( 'https://www.example.org/wp-content/plugins/fake-plugin/assets/public/scripts/0-abe0ca7ac47569747675.js', $asset_exists );
  }

  public function test_get_assets_manifest_item_does_not_exist() {
    // Needed so that GLOBAL_VARIABLE_NAME is defined.
    ( new Manifest() )->register_global_variable();

    $this->expectException( Missing_Manifest::class );

    $asset_does_not_exists = ( new Manifest() )->get_assets_manifest_item( 'test.js' );

    $this->assertEmpty( $missing_asset );
  }

  public function test_get_decoded_manifest_data_works() {
    // Needed so that GLOBAL_VARIABLE_NAME is defined.
    ( new Manifest() )->register_global_variable();

    $data = ( new Manifest() )->get_decoded_manifest_data();

    $this->assertIsArray( $data );
    $this->assertArrayHasKey( self::SCRIPT, $data );
  }
}
