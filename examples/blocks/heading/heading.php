<?php
/**
 * All info regarding the Heading block
 *
 * @since 1.0.0
 * @package Inf_Theme\Blocks\Heading
 */

namespace Inf_Theme\Blocks\Heading;

use Eightshift_Libs\Blocks\Base_Block;

/**
 * Class Heading
 */
class Heading extends Base_Block {

  /**
   * Block's name.
   *
   * @since 1.0.0
   */
  const NAME = 'heading';

  /**
   * Get block attributes assigned inside block class.
   *
   * @return array
   *
   * @since 1.0.0
   */
  public function get_block_attributes() : array {
    return array(
      'content' => array(
        'type' => 'string',
      ),
      'level' => array(
        'type' => 'int',
        'default' => '2',
      ),
      'styleAlign' => array(
        'type' => 'string',
        'default' => 'center',
      ),
      'styleSize' => array(
        'type' => 'string',
        'default' => 'huge',
      ),
    );
  }
}
