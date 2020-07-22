<?php

namespace EightshiftLibs\Cli;

use EightshiftLibs\Services\ServiceInterface;

interface CliInterface extends ServiceInterface {

  public function __invoke( array $args );

  public function register_command();

  public function get_command_name() : string;

  public function get_class_name() : string;

  public function callback( array $args );
}
