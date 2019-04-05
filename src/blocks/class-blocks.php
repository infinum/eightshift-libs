<?php
/**
 * Register all blocks for the plugin.
 *
 * @since 1.0.0
 * @package Eightshift_Libs\Blocks
 */

namespace Eightshift_Libs\Blocks;

use Eightshift_Libs\Core\Service;

/**
 * Class Blocks
 */
class Blocks implements Service {

  /**
   * The array of blocks that should be registered.
   *
   * @since 1.0.0
   * @access   protected
   * @var      array    $actions    The actions registered with WordPress to fire when the plugin loads.
   */
  public $list;

  /**
   * Initialize the list of used blocks.
   *
   * @since 1.0.0
   *
   * TODO: Implement service locator.
   */
  public function __construct() {
    $this->list = array(
      'section'         => new Section\Section(),
      'heading'         => new Heading\Heading(),
      'cf7-form'        => new Cf7_Form\Cf7_Form(),
      'paragraph'       => new Paragraph\Paragraph(),
      'image'           => new Image\Image(),
      'image-gallery'   => new Image_Gallery\Image_Gallery(),
      'cta-box'         => new Cta_Box\Cta_Box(),
      'button'          => new Button\Button(),
      'testimonials'    => new Testimonials\Testimonials(),
      'gallery'         => new Gallery\Gallery(),
      'location-map'    => new Location_Map\Location_Map(),
      'image-text'      => new Image_Text\Image_Text(),
    );
  }

  /**
   * Register all the hooks
   *
   * @since 1.0.0
   */
  public function register() : void {
    add_action( 'init', [ $this, 'register_blocks' ] );
  }

  /**
   * Add a new action to the collection to be registered with WordPress.
   *
   * @throws \Exception If there's a block in $this->list that doesn't extend "Block" class.
   *
   * @return void
   *
   * @since 1.0.0
   */
  public function register_blocks() : void {
    if ( ! empty( $this->list ) ) {
      foreach ( $this->list as $block ) {
        if ( ! ( $block instanceof Block ) ) {
          throw new \Exception( 'Trying to register a block that doesn\'t extend "Block" class' );
        }
        $this->register_block( $block );
      }
    }
  }

  /**
   * Registers a dynamic block with a corresponding render_callback defined in the block itself
   *
   * @param Block $block Block object.
   *
   * @return void
   */
  protected function register_block( Block $block ) {

    $block->add_default_attributes();

    register_block_type(
      $block::BLOCK_NAMESPACE . '/' . $block::NAME,
      array(
        'render_callback' => array( $block, 'render' ),
        'attributes' => $block->attributes,
      )
    );

  }
}
