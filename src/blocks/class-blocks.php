<?php
/**
 * Class Blocks holds base abstract class for Gutenberg blocks registration.
 * It provides ability to register custom blocks using manifest.json setup.
 *
 * @package Eightshift_Libs\Blocks
 */

namespace Eightshift_Libs\Blocks;

use Eightshift_Libs\Core\Service;
use Eightshift_Libs\Blocks\Renderable_Block;
use Eightshift_Libs\Exception\Missing_Block_Wrapper_View;
use Eightshift_Libs\Exception\Missing_Block_View_Helper;
use Eightshift_Libs\Exception\Missing_Block_View;
use Eightshift_Libs\Blocks\Blocks_Full_Data;
use Eightshift_Libs\Exception\Missing_Blocks_Manifest;
use Eightshift_Libs\Exception\Missing_Block_Namespace;
use Eightshift_Libs\Core\Config_Data;

/**
 * Class Blocks
 *
 * @since 2.0.0
 */
class Blocks implements Service, Renderable_Block {

  /**
   * Instance variable of project config data.
   *
   * @var object
   *
   * @since 2.0.0
   */
  protected $config;

  /**
   * Create a new instance that injects config data to get project specific details.
   *
   * @param Config_Data $config Inject config which holds data regarding project details.
   *
   * @since 2.0.0
   */
  public function __construct( Config_Data $config ) {
    $this->config = $config;
  }

  /**
   * Register all the hooks
   *
   * @since 2.0.0
   */
  public function register() {
    // // Register all custom blocks.
    add_action( 'init', [ $this, 'register_blocks' ] );

    // Remove P tags from content.
    remove_filter( 'the_content', 'wpautop' );
  }

  /**
   * Get all blocks with full block name.
   * Used to limit what blocks are going to be used in your project.
   *
   * @return array
   *
   * @since 2.0.0
   */
  public function get_all_blocks_list() : array {
    $blocks = array_map(
      function( $block ) {
        return $block['blockFullName'];
      },
      $this->get_blocks()
    );

    // Allow reusable block.
    $blocks[] = 'core/block';

    return $blocks;
  }

  /**
   * Method used to register all custom blocks with data fetched from blocks manifest.json.
   *
   * @return void
   *
   * @since 2.0.0
   */
  public function register_blocks() {
    $blocks = $this->get_data()['blocks'];

    if ( ! empty( $blocks ) ) {
      \array_map(
        function( $block ) {
          $this->register_block( $block );
        },
        $blocks
      );
    }
  }

  /**
   * Method used to really register Gutenberg blocks.
   * It uses native register_block_type method from WP.
   * Render method is provided depending on the hasWrapper key.
   *
   * @param array $block_details Block Manifest details.
   *
   * @return void
   *
   * @since 2.0.0
   */
  public function register_block( array $block_details ) {
    $render = $block_details['hasWrapper'] ? 'render_wrapper' : 'render';

    register_block_type(
      $block_details['blockFullName'],
      array(
        'render_callback' => [ $this, $render ],
        'attributes' => $this->get_attributes( $block_details ),
      )
    );
  }

  /**
   * Get blocks attributes.
   * This method combines default, block and wrapper attributes.
   * Default attributes are hardcoded in this lib.
   * Block attributes are provided by block manifest.json file.
   * Wrapper attributes are provided by wrapper manifest.json file and is only available if block has `hasWrapper:true` settings.
   *
   * @param array $block_details Block Manifest details.
   *
   * @return array
   */
  public function get_attributes( array $block_details ) : array {

    $block_name      = $block_details['blockName'];
    $block_full_name = $block_details['blockFullName'];

    $default_attributes      = [
      'blockName' => array(
        'type' => 'string',
        'default' => $block_name,
      ),
      'blockFullName' => array(
        'type' => 'string',
        'default' => $block_full_name,
      ),
      'blockClass' => array(
        'type' => 'string',
        'default' => "block-{$block_name}",
      ),
      'blockJsClass' => array(
        'type' => 'string',
        'default' => "js-block-{$block_name}",
      ),
    ];

    $block_attributes        = $block_details['attributes'];
    $block_shared_attributes = ( $block_details['hasWrapper'] === true ) ? $this->get_data()['wrapper']['attributes'] : [];

    return array_merge(
      $default_attributes,
      $block_attributes,
      $block_shared_attributes
    );
  }

  /**
   * Provides block registration render wrapper callback method.
   * If block is using `hasWrapper:true` setting view method is first routed through wrapper component view and then in block view.
   *
   * @param array  $attributes          Array of attributes as defined in block's manifest.json.
   * @param string $inner_block_content Block's content.
   *
   * @throws Exception\Missing_Block_Wrapper_View Throws error if wrapper component view is missing.
   * @throws Exception\Missing_Block_View         Throws error if block view is missing.
   *
   * @return string Html template for block.
   *
   * @since 2.0.0
   */
  public function render_wrapper( array $attributes, $inner_block_content ) : string {

    // Block details is unavailable in this method so we are fetching block name via attributes.
    $block_name = $attributes['blockName'] ?? '';

    // Get block view path.
    $template_path = $this->get_block_view_path( $block_name );

    // Get block wrapper view path.
    $wrapper_path = "{$this->get_wrapper_path()}/wrapper.php";

    // Check if wrapper componet exists.
    if ( ! file_exists( $wrapper_path ) ) {
      throw Missing_Block_Wrapper_View::view_wrapper_exception( $block_name, $wrapper_path );
    }

    // Check if actual block exists.
    if ( ! file_exists( $template_path ) ) {
      throw Missing_Block_View::view_exception( $block_name, $template_path );
    }

    // If everything is ok, return the contents of the template (return, NOT echo).
    ob_start();
    include $wrapper_path;
    $output = ob_get_clean();
    unset( $block_name, $template_path, $wrapper_path, $attributes, $inner_block_content );
    return $output;
  }

  /**
   * Provides block registration render normal callback method.
   * If block is using `hasWrapper:false` setting view method is provides in block.
   *
   * @param array  $attributes          Array of attributes as defined in block's manifest.json.
   * @param string $inner_block_content Block's content.
   *
   * @throws Exception\Missing_Block_View Throws error if block view is missing.
   *
   * @return string Html template for block.
   *
   * @since 2.0.0
   */
  public function render( array $attributes, $inner_block_content ) : string {

    // Block details is unavailable in this method so we are fetching block name via attributes.
    $block_name = $attributes['blockName'] ?? '';

    // Get block view path.
    $template_path = $this->get_block_view_path( $block_name );

    // Check if actual block exists.
    if ( ! file_exists( $template_path ) ) {
      throw Missing_Block_View::view_exception( $block_name, $template_path );
    }

    // If everything is ok, return the contents of the template (return, NOT echo).
    ob_start();
    include $template_path;
    $output = ob_get_clean();
    unset( $block_name, $template_path, $attributes, $inner_block_content );
    return $output;
  }

  /**
   * Get blocks absolute path.
   * Prefix path is defined by project config.
   *
   * @return string
   *
   * @since 2.0.0
   */
  protected function get_blocks_path() : string {
    return $this->config->get_project_path() . '/src/blocks';
  }

  /**
   * Get blocks custom folder absolute path.
   *
   * @return string
   *
   * @since 2.0.0
   */
  protected function get_blocks_custom_path() : string {
    return "{$this->get_blocks_path()}/custom";
  }

  /**
   * Get block view absolute path.
   *
   * @param string $block_name Block Name value to get a path.
   *
   * @return string
   *
   * @since 2.0.0
   */
  protected function get_block_view_path( string $block_name ) : string {
    return "{$this->get_blocks_custom_path()}/{$block_name}/{$block_name}.php";
  }

  /**
   * Get wrapper folder full absolute path.
   *
   * @return string
   *
   * @since 2.0.0
   */
  protected function get_wrapper_path() : string {
    return "{$this->get_blocks_path()}/wrapper";
  }

  /**
   * Get blocks full data for global settings, blocks and wrapper.
   * If develop env data is fetched from json otherwise data is stored in transient.
   *
   * @return array
   *
   * @since 2.0.0
   */
  protected function get_data() : array {
    $blocks   = $this->get_blocks();
    $settings = $this->get_settings();
    $wrapper  = $this->get_wrapper();

    $blocks = array_map(
      function( $block ) use ( $settings ) {

        // Add additional data to the block settings.
        $block['namespace']     = $settings['namespace'];
        $block['blockFullName'] = "{$settings['namespace']}/{$block['blockName']}";

        return $block;
      },
      $this->get_blocks()
    );

    return [
      'settings' => $settings,
      'wrapper' => $wrapper,
      'blocks' => $blocks,
    ];
  }

  /**
   * Get blocks manifest data from blocks manifest.json files.
   *
   * @throws Exception\Missing_Block_Name Throws error if block name is missing.
   *
   * @return array
   *
   * @since 2.0.0
   */
  protected function get_blocks() : array {

    return array_map(
      function( $block ) {

        $block = implode( ' ', file( ( $block ) ) );
        $block = json_decode( $block, true );

        if ( ! isset( $block['blockName'] ) ) {
          throw Missing_Block_Name::name_exception();
        }

        if ( ! isset( $block['classes'] ) ) {
          $block['classes'] = [];
        }
    
        if ( ! isset( $block['attributes'] ) ) {
          $block['attributes'] = [];
        }
    
        if ( ! isset( $block['hasWrapper'] ) ) {
          $block['hasWrapper'] = true;
        }
    
        if ( ! isset( $block['hasInnerBlocks'] ) ) {
          $block['hasInnerBlocks'] = false;
        }

        return $block;
      },
      glob( "{$this->get_blocks_custom_path()}/*/manifest.json" )
    );
  }

  /**
   * Get wrapper manifest data from wrapper manifest.json file.
   *
   * @return array
   *
   * @since 2.0.0
   */
  protected function get_wrapper() : array {
    $manifest_path = "{$this->get_wrapper_path()}/manifest.json";

    if ( ! file_exists( $manifest_path ) ) {
      throw Missing_Blocks_Manifest::manifest_exception( $manifest_path );
    }

    $settings = implode( ' ', file( ( $manifest_path ) ) );
    $settings = json_decode( $settings, true );

    return $settings;
  }

  /**
   * Get blocks global settings manifest data from settings manifest.json file.
   *
   * @throws Exception\Missing_Blocks_Manifest Throws error if blocks manifest is missing.
   * @throws Exception\Missing_Block_Namespace Throws error if block namespace is missing.
   *
   * @return array
   *
   * @since 2.0.0
   */
  protected function get_settings() : array {
    $manifest_path = $this->get_blocks_path() . '/manifest.json';

    if ( ! file_exists( $manifest_path ) ) {
      throw Missing_Blocks_Manifest::manifest_exception( $manifest_path );
    }

    $settings = implode( ' ', file( ( $manifest_path ) ) );
    $settings = json_decode( $settings, true );

    if ( ! isset( $settings['namespace'] ) ) {
      throw Missing_Block_Namespace::namespace_exception();
    }

    return $settings;
  }

  /**
   * Locate and return template part with passed attributes for wrapper.
   * Used to render php block wrapper view.
   *
   * @param string $src                  String with URL path to template.
   * @param array  $attributes           Attributes array to pass in template.
   * @param string $inner_block_content If using inner blocks content pass the data.
   *
   * @throws Exception\Missing_Wrapper_View_Helper Throws error if wrapper view template is missing.
   *
   * @since 2.0.0
   */
  public function render_wrapper_view( string $src, array $attributes, $inner_block_content = null ) {
    if ( ! file_exists( $src ) ) {
      throw Missing_Wrapper_View_Helper::view_exception( $src );
    }

    include $src;
    unset( $src, $attributes, $inner_block_content );
  }

  /**
   * Locate and return template part with passed attributes for block.
   * Used to render php block view.
   *
   * @param string $src                  String with URL path to template.
   * @param array  $attributes           Attributes array to pass in template.
   * @param string $inner_block_content If using inner blocks content pass the data.
   *
   * @throws Exception\Missing_Wrapper_View_Helper Throws error if wrapper view template is missing.
   *
   * @since 2.0.0
   */
  public function render_block_view( string $src, array $attributes, $inner_block_content = null ) {
    $path = $this->get_blocks_path() . $src;

    if ( ! file_exists( $path ) ) {
      throw Missing_Block_View_Helper::view_exception( $path );
    }

    include $path;
    unset( $src, $attributes, $inner_block_content, $path );
  }
}
