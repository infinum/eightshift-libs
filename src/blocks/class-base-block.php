<?php
/**
 * File that holds base abstract class for Gutenberg blocks registration
 *
 * @since   0.1.0
 * @package Eightshift_Libs\Blocks
 */

namespace Eightshift_Libs\Blocks;

use Eightshift_Libs\Blocks\Block;
use Eightshift_Libs\Blocks\Renderable_Block;
use Eightshift_Libs\Core\Service;
use Eightshift_Libs\Exception\Missing_Block_Name;

/**
 * Class Block
 */
abstract class Base_Block extends Attribute_Type_Enums implements Block, Service, Renderable_Block {

  /**
   * Namespace in which all our blocks exist.
   *
   * @var string
   *
   * @since 0.1.0
   */
  const BLOCK_NAMESPACE = 'eightshift';

  /**
   * Register all the hooks
   *
   * @since 0.1.0
   */
  public function register() : void {
    add_action(
      'init',
      function() {
        register_block_type(
          $this->get_block_namespace() . '/' . $this->get_block_name(),
          array(
            'render_callback' => [ $this, 'render' ],
            'attributes' => $this->get_attributes(),
          )
        );
      }
    );
  }

  /**
   * Get the block name to use to register block.
   *
   * @return string Custom blog name.
   *
   * @since 0.1.0
   */
  abstract protected function get_block_name() : string;

  /**
   * Get the block name to use to register block.
   *
   * @return string Custom blog name.
   *
   * @since 0.1.0
   */
  protected function get_block_namespace() : string {
    return static::BLOCK_NAMESPACE;
  }

  /**
   * Get block attributes assigned inside block class.
   *
   * @return array
   *
   * @since 0.1.0
   */
  abstract public function get_block_attributes() : array;

  /**
   * Get block view path.
   *
   * @return string
   *
   * @since 0.1.0
   */
  public function get_block_view_path() {
    $block_name = $this->get_block_name();

    return 'src/blocks/' . $block_name . '/view/' . $block_name . '.php';
  }

  /**
   * Adds default attributes that are dynamically built for all blocks.
   * These are:
   * - blockName: Block's full name including namespace (example: eightshift/heading)
   * - rootClass: Block's root (base) BEM CSS class, built in "block/$name" format (example: block-heading)
   * - jsClass:   Block's js selector class, built in "js-block-$name" format (example: js-block-heading)
   *
   * @throws \Exception On missing block name.
   *
   * @return array
   *
   * @since 0.1.0
   */
  public function get_default_attributes() : array {
    $block_namespace = $this->get_block_namespace();
    $block_name      = $this->get_block_name();

    return [
      'blockName' => array(
        'type' => parent::TYPE_STRING,
        'default' => "{$block_namespace}/{$block_name}",
      ),
      'rootClass' => array(
        'type' => parent::TYPE_STRING,
        'default' => "block-{$block_name}",
      ),
      'jsClass' => array(
        'type' => parent::TYPE_STRING,
        'default' => "js-block-{$block_name}",
      ),
    ];
  }

  /**
   * Get all block attributes. Default and block attributes.
   *
   * @return array
   *
   * @since 0.1.0
   */
  public function get_attributes() : array {
    return array_merge( $this->get_default_attributes(), $this->get_block_attributes() );
  }

  /**
   * Renders the block using a template in Infinum\Blocks\Templates namespace/folder.
   * Template file must have the same name as the class-blockname file, for example:
   *
   *   Block:     class-heading.php
   *   Template:  heading.php
   *
   * @param array  $attributes Array of attributes as defined in block's index.js.
   * @param string $content    Block's content.
   *
   * @throws Missing_Block::view_exception On missing attributes OR missing template.
   * @return string html template for block.
   *
   * @since 0.1.0
   */
  public function render( array $attributes, string $content ) : string {
    $template_path = $this->get_block_view_path();

    $template = locate_template( $template_path );
    if ( empty( $template ) ) {
      throw Missing_Block::view_exception( $this->get_block_name(), $template_path );
    }

    // If everything is ok, return the contents of the template (return, NOT echo).
    ob_start();
    include $template;
    $output = ob_get_clean();
    unset( $template );
    return $output;
  }
}
