<?php
/**
 * File that holds base abstract class for Gutenberg blocks registration
 *
 * @since   1.0.0
 * @package Eightshift_Libs\Blocks
 */

namespace Eightshift_Libs\Blocks;

use Eightshift_Libs\Blocks\Block;
use Eightshift_Libs\Core\Service;
use Eightshift_Libs\Exception\Missing_Block_Name;

/**
 * Class Block
 */
abstract class Base_Block extends Attribute_Type_Enums implements Block {

  /**
   * Block Name.
   *
   * @var string
   *
   * @since 1.0.0
   */
  const NAME = 'abstract-block';

  /**
   * Namespace in which all our blocks exist.
   *
   * @var string
   *
   * @since 1.0.0
   */
  const BLOCK_NAMESPACE = 'eightshift';

  /**
   * Register all the hooks
   *
   * @since 1.0.0
   */
  public function register() : void {
    add_action(
      'init',
      function() {
        register_block_type(
          static::BLOCK_NAMESPACE . '/' . static::NAME,
          array(
            'render_callback' => [ $this, 'render' ],
            'attributes' => $this->get_attributes(),
          )
        );
      }
    );
  }

  /**
   * Adds default attributes that are dynamically built for all blocks.
   * These are:
   * - blockName: Block's full name including namespace (example: infinum/heading)
   * - rootClass: Block's root (base) BEM CSS class, built in "block/$name" format (example: block-heading)
   * - jsClass:   Block's js selector class, built in "js-block-$name" format (example: js-block-heading)
   *
   * @throws \Exception On missing block name.
   *
   * @return array
   *
   * @since 1.0.0
   */
  public function get_default_attributes() : array {

    // Make sure the class (block) extending this class (abstract Base_Block)
    // has defined its own name.
    if ( static::NAME === self::NAME ) {
      throw Missing_Block::name_exception();
    }

    return [
      'blockName' => array(
        'type' => parent::TYPE_STRING,
        'default' => self::BLOCK_NAMESPACE . '/' . static::NAME,
      ),
      'rootClass' => array(
        'type' => parent::TYPE_STRING,
        'default' => 'block-' . static::NAME,
      ),
      'jsClass' => array(
        'type' => parent::TYPE_STRING,
        'default' => 'js-block-' . static::NAME,
      ),
    ];
  }

  /**
   * Get block attributes assigned inside block class.
   *
   * @return array
   *
   * @since 1.0.0
   */
  public function get_block_attributes() : array {
    return [];
  }

  /**
   * Get all block attributes. Default and block attributes.
   *
   * @return array
   *
   * @since 1.0.0
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
   * @throws \Exception On missing attributes OR missing template.
   * @echo   string
   *
   * @since 1.0.0
   */
  public function render( array $attributes, string $content ) : string {

    // Block must have a defined name to find its template.
    // Make sure the class (block) extending this class (abstract Base_Block)
    // has defined its own name.
    if ( static::NAME === self::NAME ) {
      throw Missing_Block::name_exception();
    }

    $template_path = 'src/blocks/' . static::NAME . '/view/' . static::NAME . '.php';
    $template      = locate_template( $template_path );

    if ( empty( $template ) ) {
      throw Missing_Block::view_exception( static::NAME, $template_path );
    }

    // If everything is ok, return the contents of the template (return, NOT echo).
    ob_start();
    include $template;
    $output = ob_get_clean();
    unset( $template );
    return $output;
  }
}
