<?php

namespace EightshiftLibs\Cli;

interface CliInterface {

    /**
   * Register method for WPCLI command
   * 
   * @param string $command_parent_name Define top level commands name.
   *
   * @return void
   */
  public function register( string $command_parent_name );

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
   * Get WPCLI command doc.
   *
   * @return string
   */
  public function get_doc() : array;
}
