<?php
/**
 * Abstract class that holds all methods for WPCLI options.
 *
 * @package EightshiftLibs\Cli
 */

namespace EightshiftLibs\Cli;

/**
 * Class AbstractCli
 */
abstract class AbstractCli implements CliInterface {

  /**
   * CLI helpers trait.
   */
  use CliHelpers;

  /**
   * Top level commands name.
   */
  protected $command_parent_name;

  /**
   * Output dir relative path.
   */
  const OUTPUT_DIR = '';

  /**
   * Output template name.
   */
  const TEMPLATE = '';

  /**
   * Register method for WPCLI command
   * 
   * @param string $command_parent_name Define top level commands name.
   *
   * @return void
   */
  public function register( string $command_parent_name ) {
    $this->command_parent_name = $command_parent_name;

    \add_action( 'cli_init', [ $this, 'register_command'] );
  }

  /**
   * Define global synopsis for all projects commands.
   *
   * @return array
   */
  public function get_global_synopsis() : array {
    return [
      'synopsis' => [
        [
          'type'        => 'assoc',
          'name'        => 'namespace',
          'description' => 'Define your projects namespace. Default is read from composer autoload psr-4 key.',
          'optional'    => true,
        ],
        [
          'type'        => 'assoc',
          'name'        => 'vendor_prefix',
          'description' => 'Define your projects vendor_prefix. Default is read from composer extra, imposter, namespace key.',
          'optional'    => true,
        ],
        [
          'type'        => 'assoc',
          'name'        => 'config_path',
          'description' => 'Define your projects composer apsolute path.',
          'optional'    => true,
        ],
      ],
    ];
  }

  /**
   * Method that creates actual WPCLI command in terminal.
   *
   * @return void
   */
  public function register_command() {
    \WP_CLI::add_command(
      $this->command_parent_name . ' ' . $this->get_command_name(),
      $this->get_class_name(),
      array_merge(
        $this->get_global_synopsis(),
        $this->get_doc(),
      )
    );
  }

  /**
   * Define default develop props.
   *
   * @param array $args WPCLI eval-file arguments.
   *
   * @return array
   */
  public function get_develop_args( array $args ) : array {
    return $args;
  }

}
