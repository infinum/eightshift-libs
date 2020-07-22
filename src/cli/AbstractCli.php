<?php

namespace EightshiftLibs\Cli;

abstract class AbstractCli implements CliInterface {
  /**
   * Project root
   */
  protected $root;

  public function register() {
    \add_action( 'cli_init', [ $this, 'register_command'] );
  }

  public function __invoke( array $args ) {
    \WP_CLI::success( $args[0] );
  }


  public function register_command() {
    \WP_CLI::add_command( $this->get_command_name(), $this->get_class_name() );
  }
}
