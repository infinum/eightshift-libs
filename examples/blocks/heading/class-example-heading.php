<?php
/**
 * All info regarding the Heading block
 *
 * @since 1.0.0
 * @package Custom_Namespace\Blocks\Heading
 *
 * TODO: Refactor and test
 */

namespace Custom_Namespace\Blocks\Heading;

use Eightshift_Libs\Blocks\Base_Block;

/**
 * Class Heading
 */
class Example_Heading extends Base_Block {

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
        'type' => parent::TYPE_STRING,
      ),
      'level' => array(
        'type' => parent::TYPE_NUMBER,
        'default' => '2',
      ),
      'styleAlign' => array(
        'type' => parent::TYPE_STRING,
        'default' => 'center',
      ),
      'styleSize' => array(
        'type' => parent::TYPE_STRING,
        'default' => 'huge',
      ),
    );
  }
}
