<?php
/**
 * The initial unit test setup file
 *
 * Used to make BrainMonkey testsuite work in our unit tests.
 *
 * @since   0.9.0
 * @package Eightshift_Libs\Tests\Unit
 */

namespace Eightshift_Libs\Tests\Unit;

use \PHPUnit\Framework\TestCase;
use \Brain\Monkey;
use \Brain\Monkey\Functions;

/**
 * Initial test case
 *
 * This abstract class will be extended in all other unit test classes.
 */
abstract class Init_Test_Case extends TestCase {
  protected function setUp() {
    parent::setUp();
    Monkey\setUp();

    Functions\stubs(
      [
        'esc_attr',
        'esc_html',
        'esc_textarea',
        '__',
        '_x',
        'esc_html__',
        'esc_html_e',
        'esc_attr_e',
        'esc_html_x',
        'esc_attr_x',
      ]
    );
  }

  protected function tearDown() {
    Monkey\tearDown();
    parent::tearDown();
  }
}
