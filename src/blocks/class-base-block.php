<?php
/**
 * File that holds base abstract class for Gutenberg blocks registration
 *
 * @since   0.3.0 Separating Wrapper_Block and General_Block.
 * @since   0.1.0
 * @package Eightshift_Libs\Blocks
 */

namespace Eightshift_Libs\Blocks;

use Eightshift_Libs\Blocks\Block;
use Eightshift_Libs\Blocks\Renderable_Block;
use Eightshift_Libs\Core\Service;

/**
 * Class Block
 */
abstract class Base_Block extends Attribute_Type_Enums implements Block, Service, Renderable_Block {

  /**
   * Block name constant that you define in project block implementation.
   *
   * @var string
   *
   * @since 0.3.0
   */
  const BLOCK_NAME = '';

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
   * Get the block name used to register block.
   *
   * @return string Custom block name.
   *
   * @since 0.1.0
   */
  protected function get_block_name() : string {
    return static::BLOCK_NAME;
  }

  /**
   * Get the block namespace used to register block.
   *
   * @return string Custom block name.
   *
   * @since 0.1.0
   */
  protected function get_block_namespace() : string {
    return static::BLOCK_NAMESPACE;
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
   * @since 0.3.0
   */
  public function get_block_view_path() {
    $block_name = $this->get_block_name();

    return 'src/blocks/' . $block_name . '/view/' . $block_name . '.php';
  }
}
