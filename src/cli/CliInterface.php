<?php

namespace EightshiftLibs\Cli;

use EightshiftLibs\Services\ServiceInterface;

interface CliInterface extends ServiceInterface {

  /**
   * Call internal method for passing arguments
   *
   * @param array $args Array of arguments form terminal
   *
   * @return void
   */
  public function __invoke( array $args );

  /**
   * Method that creates actual WPCLI command in terminal.
   *
   * @return void
   */
  public function register_command();

  /**
   * Get WPCLI command name
   *
   * @return string
   */
  public function get_command_name() : string;

  /**
   * Get WPCLI trigger class name.
   *
   * @return string
   */
  public function get_class_name() : string;

  /**
   * Get WPCLI command callback.
   *
   * @param array $args Arguments provided.
   *
   * @return void
   */
  public function callback( array $args );
}
