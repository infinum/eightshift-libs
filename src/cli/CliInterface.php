<?php

namespace EightshiftLibs\Cli;

interface CliInterface {

    /**
   * Register method for WPCLI command
   *
   * @return void
   */
  public function register() : void;

  /**
   * Call internal method for passing arguments
   *
   * @param array $args Array of arguments form terminal
   *
   * @return void
   */
  public function __invoke( array $args, array $assoc_args );

  /**
   * Method that creates actual WPCLI command in terminal.
   *
   * @return void
   */
  public function register_command() : void;

  /**
   * Get WPCLI command name
   *
   * @return string
   */
  public static function get_command_name() : string;

  /**
   * Get WPCLI trigger class name.
   *
   * @return string
   */
  public function get_class_name() : string;

  /**
   * Get WPCLI command doc.
   *
   * @return string
   */
  public function get_doc() : array;
}
