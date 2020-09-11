<?php
/**
 * Abstract class that holds all methods for WPCLI options.
 *
 * @package EightshiftLibs\Cli
 */

declare( strict_types=1 );

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
   *
   * @var string
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
   * Construct Method.
   *
   * @param string $command_parent_name Define top level commands name.
   *
   * @return void
   */
  public function __construct( $command_parent_name ) {
    $this->command_parent_name = $command_parent_name;
  }

  /**
   * Register method for WPCLI command.
   *
   * @return void
   */
  public function register() : void {
    \add_action( 'cli_init', [ $this, 'register_command' ] );
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
  public function register_command() : void {
    $reflection_class = new \ReflectionClass( $this->get_class_name() );
    $class            = $reflection_class->newInstanceArgs( [ $this->command_parent_name ] );

    \WP_CLI::add_command(
      $this->command_parent_name . ' ' . $this->get_command_name(),
      $class,
      array_merge(
        $this->get_global_synopsis(),
        $this->get_doc()
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

  /**
   * Get full class name for current class.
   *
   * @return string
   */
  public function get_class_name() : string {
    return get_class( $this );
  }

  /**
   * Get short class name for current class.
   *
   * @return string
   */
  public function get_class_short_name() : string {
    $arr = explode( '\\', $this->get_class_name() );

    return str_replace( 'Cli', '', end( $arr ) );
  }

  /**
   * Get WPCLI command name
   *
   * @return string
   */
  public function get_command_name() : string {
    return 'create_' . strtolower( preg_replace( '/(?<!^)[A-Z]/', '_$0', $this->get_class_short_name() ) );
  }

  /**
   * Get WPCLI command doc.
   *
   * @return string
   */
  public function get_doc() : array {
    return [];
  }

}
