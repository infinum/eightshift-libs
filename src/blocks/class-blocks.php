<?php
/**
 * Class Blocks is the base class for Gutenberg blocks registration.
 * It provides the ability to register custom blocks using manifest.json.
 *
 * @package Eightshift_Libs\Blocks
 */

declare( strict_types=1 );

namespace Eightshift_Libs\Blocks;

use Eightshift_Libs\Core\Config_Data;
use Eightshift_Libs\Core\Service;
use Eightshift_Libs\Exception\Invalid_Block;
use Eightshift_Libs\Exception\Invalid_Manifest;

/**
 * Class Blocks
 *
 * @since 2.0.0
 */
class Blocks implements Service, Renderable_Block {

  /**
   * Instance variable of project config data.
   *
   * @var Config_Data
   *
   * @since 2.0.0
   */
  protected $config;

  /**
   * Full data of blocks, settings and wrapper data.
   *
   * @var array
   *
   * @since 2.0.0
   */
  protected $blocks = [];

  /**
   * Block view filter name constant.
   *
   * @var string
   *
   * @since 2.0.0
   */
  const BLOCK_VIEW_FILTER_NAME = 'block-view-data';

  /**
   * Block attributes override filter name constant.
   *
   * @var string
   *
   * @since 2.0.0
   */
  const BLOCK_ATTRIBUTES_FILTER_NAME = 'block-attributes-override';

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
   * @return void
   *
   * @since 2.0.0
   */
  public function register() {

    // // Register all custom blocks.
    add_action( 'init', [ $this, 'get_blocks_data_full_raw' ], 10 );
    add_action( 'init', [ $this, 'register_blocks' ], 11 );

    // Remove P tags from content.
    remove_filter( 'the_content', 'wpautop' );

    // Create new custom category for custom blocks.
    add_filter( 'block_categories', [ $this, 'get_custom_category' ] );

    add_action( 'after_setup_theme', [ $this, 'add_theme_support' ], 25 );

    add_action( 'after_setup_theme', [ $this, 'change_editor_color_palette' ], 11 );
  }

  /**
   * Create custom project color palette.
   * This colors are fetched from the main manifest.json file located in src>blocks folder.
   *
   * @return void
   */
  public function change_editor_color_palette() {

    $colors = $this->get_colors_from_settings();

    if ( $colors ) {
      \add_theme_support( 'editor-color-palette', $colors );
    }
  }

  /**
   * Function to read and return all colors as defined in block global settings
   *
   * @return array
   */
  public function get_colors_from_settings() {
    return $this->get_settings()['globalVariables']['colors'] ?? [];
  }

  /**
   * Register align wide option in editor
   *
   * @return void
   *
   * @since 2.0.0
   */
  public function add_theme_support() {
    add_theme_support( 'align-wide' );
  }

  /**
   * Get blocks full data from global settings, blocks and wrapper.
   * You should never call this method directly instead you should call $this->blocks.
   *
   * @return void
   *
   * @since 2.0.0
   */
  public function get_blocks_data_full_raw() {

    if ( ! $this->blocks ) {
      $settings = $this->get_settings();
      $wrapper  = $this->get_wrapper();

      $blocks = array_map(
        function( $block ) use ( $settings ) {

          // Add additional data to the block settings.
          $namespace = $block['namespace'] ?? '';

          // Check if namespace is defined in block or in global manifest settings.
          $block['namespace']     = ! empty( $namespace ) ? $namespace : $settings['namespace'];
          $block['blockFullName'] = "{$block['namespace']}/{$block['blockName']}";

          return $block;
        },
        $this->get_blocks_data()
      );

      $this->blocks = [
        'settings' => $settings,
        'wrapper' => $wrapper,
        'blocks' => $blocks,
      ];
    }
  }

  /**
   * Get all blocks with full block name.
   * Used to limit what blocks are going to be used in your project using allowed_block_types filter.
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
      $this->blocks['blocks']
    );

    // Allow reusable block.
    $blocks[] = 'core/block';
    $blocks[] = 'core/template';

    return $blocks;
  }

  /**
   * Method used to register all custom blocks with data fetched from blocks manifest.json.
   *
   * @throws Invalid_Block Throws error if blocks are missing.
   *
   * @return void
   *
   * @since 2.0.0
   */
  public function register_blocks() {
    $blocks = $this->blocks['blocks'];

    if ( empty( $blocks ) ) {
      throw Invalid_Block::missing_blocks_exception();
    }

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
   *
   * @param array $block_details Full Block Manifest details.
   *
   * @return void
   *
   * @since 2.0.0
   */
  public function register_block( array $block_details ) {
    \register_block_type(
      $block_details['blockFullName'],
      array(
        'render_callback' => [ $this, 'render' ],
        'attributes' => $this->get_attributes( $block_details ),
      )
    );
  }

  /**
   * Provides block registration callback method for render when using wrapper option.
   *
   * @param array  $attributes          Array of attributes as defined in block's manifest.json.
   * @param string $inner_block_content Block's content if using inner blocks.
   *
   * @throws Invalid_Block Throws error if block wrapper view is missing.
   * @throws Invalid_Block Throws error if block view is missing.
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

    // Get block wrapper view path.
    $wrapper_path = "{$this->get_wrapper_path()}/wrapper.php";

    // Check if wrapper component exists.
    if ( ! file_exists( $wrapper_path ) ) {
      throw Invalid_Block::missing_wrapper_view_exception( $wrapper_path );
    }

    // Check if actual block exists.
    if ( ! file_exists( $template_path ) ) {
      throw Invalid_Block::missing_view_exception( $block_name, $template_path );
    }

    // If everything is ok, return the contents of the template (return, NOT echo).
    ob_start();
    include $wrapper_path;
    $output = ob_get_clean();
    unset( $block_name, $template_path, $wrapper_path, $attributes, $inner_block_content );
    return (string) $output;
  }

  /**
   * Create custom category to assign all custom blocks.
   * This category will show on all blocks list in "Add Block" button.
   *
   * @param array $categories Array of all blocks categories.
   * @return array
   *
   * @since 2.0.0
   */
  public function get_custom_category( $categories ) {
    return array_merge(
      $categories,
      [
        [
          'slug'  => 'eightshift',
          'title' => \esc_html__( 'Eightshift', 'eightshift-libs' ),
          'icon'  => 'admin-settings',
        ],
      ]
    );
  }

  /**
   * Locate and return template part with passed attributes for wrapper.
   * Used to render php block wrapper view.
   *
   * @param string $src                  String with URL path to template.
   * @param array  $attributes           Attributes array to pass in template.
   * @param string $inner_block_content If using inner blocks content pass the data.
   *
   * @return void Includes an HTML view, or throws an error if the view is missing.
   *
   * @throws Invalid_Block Throws error if wrapper view template is missing.
   *
   * @since 2.0.0
   */
  public function render_wrapper_view( string $src, array $attributes, $inner_block_content = null ) : void {
    if ( ! file_exists( $src ) ) {
      throw Invalid_Block::missing_wrapper_view_exception( $src );
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
   * @return void Includes an HTML view, or throws an error if the view is missing.
   *
   * @throws Invalid_Block Throws error if render block view is missing.
   *
   * @since 2.0.0
   */
  public function render_block_view( string $src, array $attributes, $inner_block_content = null ) : void {
    $path = $this->get_blocks_path() . $src;

    if ( ! file_exists( $path ) ) {
      throw Invalid_Block::missing_render_view_exception( $path );
    }

    include $path;
    unset( $src, $attributes, $inner_block_content, $path );
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
   * Get wrapper manifest data from wrapper manifest.json file.
   *
   * @throws Invalid_Block Throws error if wrapper settings manifest.json is missing.
   *
   * @return array
   *
   * @since 2.0.0
   */
  protected function get_wrapper() : array {
    $manifest_path = "{$this->get_wrapper_path()}/manifest.json";

    if ( ! file_exists( $manifest_path ) ) {
      throw Invalid_Block::missing_wrapper_manifest_exception( $manifest_path );
    }

    $settings = implode( ' ', (array) file( ( $manifest_path ) ) );
    $settings = json_decode( $settings, true );

    return $settings;
  }

  /**
   * Get blocks global settings manifest data from settings manifest.json file.
   *
   * @throws Invalid_Block Throws error if global settings manifest.json is missing.
   * @throws Invalid_Block Throws error if global manifest settings key namespace is missing.
   *
   * @return array
   *
   * @since 2.0.0
   */
  protected function get_settings() : array {
    $manifest_path = "{$this->get_blocks_path()}/manifest.json";

    if ( ! file_exists( $manifest_path ) ) {
      throw Invalid_Block::missing_settings_manifest_exception( $manifest_path );
    }

    $settings = implode( ' ', (array) file( ( $manifest_path ) ) );
    $settings = json_decode( $settings, true );

    if ( ! isset( $settings['namespace'] ) ) {
      throw Invalid_Block::missing_namespace_exception();
    }

    return $settings;
  }

  /**
   * Get blocks attributes.
   * This method combines default, block and wrapper attributes.
   * Default attributes are hardcoded in this lib.
   * Block attributes are provided by block manifest.json file.
   *
   * @param array $block_details Block Manifest details.
   *
   * @return array
   */
  protected function get_attributes( array $block_details ) : array {

    $block_name = $block_details['blockName'];

    $output = array_merge(
      [
        'blockName' => array(
          'type' => 'string',
          'default' => $block_name,
        ),
        'blockFullName' => array(
          'type' => 'string',
          'default' => $block_details['blockFullName'],
        ),
        'blockClass' => array(
          'type' => 'string',
          'default' => "block-{$block_name}",
        ),
        'blockJsClass' => array(
          'type' => 'string',
          'default' => "js-block-{$block_name}",
        ),
      ],
      $this->blocks['wrapper']['attributes'],
      $block_details['attributes']
    );

    $filter_name = $this->config->get_config( static::BLOCK_ATTRIBUTES_FILTER_NAME );

    if ( \has_filter( $filter_name ) ) {
      $override_attributes = \apply_filters( $filter_name, $output );

      $output = array_merge( $output, $override_attributes );
    }

    return $output;
  }

  /**
   * Throws error if manifest key blockName is missing.
   * You should never call this method directly.
   *
   * @throws Invalid_Block Throws error if block name is missing.
   *
   * @return array
   *
   * @since 2.0.0
   */
  private function get_blocks_data() : array {

    return array_map(
      function( string $block_path ) {
        $block = implode( ' ', (array) file( ( $block_path ) ) );

        $block = $this->parse_manifest( $block );

        if ( ! isset( $block['blockName'] ) ) {
          throw Invalid_Block::missing_name_exception( $block_path );
        }

        if ( ! isset( $block['classes'] ) ) {
          $block['classes'] = [];
        }

        if ( ! isset( $block['attributes'] ) ) {
          $block['attributes'] = [];
        }

        if ( ! isset( $block['hasInnerBlocks'] ) ) {
          $block['hasInnerBlocks'] = false;
        }

        return $block;
      },
      (array) glob( "{$this->get_blocks_custom_path()}/*/manifest.json" )
    );
  }

  /**
   * Helper method to check the validity of JSON string
   *
   * @link https://stackoverflow.com/a/15198925/629127
   *
   * @param string $string JSON string to validate.
   * @return array Parsed JSON string into an array.
   * @throws Invalid_Manifest Error in the case json file has errors.
   *
   * @since 2.0.0
   */
  private function parse_manifest( string $string ) : array {

    $result = json_decode( $string, true );

    switch ( json_last_error() ) {
      case JSON_ERROR_NONE:
          $error = '';
            break;
      case JSON_ERROR_DEPTH:
          $error = esc_html__( 'The maximum stack depth has been exceeded.', 'eightshift-libs' );
            break;
      case JSON_ERROR_STATE_MISMATCH:
          $error = esc_html__( 'Invalid or malformed JSON.', 'eightshift-libs' );
            break;
      case JSON_ERROR_CTRL_CHAR:
          $error = esc_html__( 'Control character error, possibly incorrectly encoded.', 'eightshift-libs' );
            break;
      case JSON_ERROR_SYNTAX:
          $error = esc_html__( 'Syntax error, malformed JSON.', 'eightshift-libs' );
            break;
      case JSON_ERROR_UTF8:
          $error = esc_html__( 'Malformed UTF-8 characters, possibly incorrectly encoded.', 'eightshift-libs' );
            break;
      case JSON_ERROR_RECURSION:
          $error = esc_html__( 'One or more recursive references in the value to be encoded.', 'eightshift-libs' );
            break;
      case JSON_ERROR_INF_OR_NAN:
          $error = esc_html__( 'One or more NAN or INF values in the value to be encoded.', 'eightshift-libs' );
            break;
      case JSON_ERROR_UNSUPPORTED_TYPE:
          $error = esc_html__( 'A value of a type that cannot be encoded was given.', 'eightshift-libs' );
            break;
      default:
          $error = esc_html__( 'Unknown JSON error occured.', 'eightshift-libs' );
            break;
    }

    if ( $error !== '' ) {
      throw Invalid_Manifest::manifest_structure_exception( $error );
    }

    return $result;
  }
}
