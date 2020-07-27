<?php
/**
 * The class file that holds abstract class for WPCLI
 *
 * @package EightshiftLibs\Cli
 */

namespace EightshiftLibs\Cli;

use EightshiftLibs\Config\ConfigCli;
use EightshiftLibs\I18n\I18nCli;
use EightshiftLibs\Main\MainCli;
use EightshiftLibs\Manifest\ManifestCli;
use EightshiftLibs\Media\MediaCli;
use EightshiftLibs\Menu\MenuCli;
use EightshiftLibs\Rest\Fields\FieldCli;
use EightshiftLibs\Rest\Routes\RouteCli;

/**
 * Class Cli
 */
class Cli {

  /**
   * Top level commands name.
   */
  protected $command_parent_name;

  /**
   * Define test output namespace.
   */
  const NAMESPACE = 'EightshiftBoilerplate';

    /**
   * Define test output vendor prefix.
   */
  const VENDOR_PREFIX = 'EightshiftBoilerplateVendor';

  /**
   * Run all CLI commands
   *
   * @param array $args WPCLI eval-file arguments.
   *
   * @return void
   */
  public function run_develop( array $args = [] ) {

    $command_name = $args[0] ?? '';

    switch ( $command_name ) {
      case 'reset':
        $this->run_command(
          new CliReset(),
          []
        );
        break;

      case 'create_config':
        $this->run_command(
          new ConfigCli()
        );
        break;

      case 'create_i18n':
        $this->run_command(
          new I18nCli()
        );
        break;

      case 'create_main':
        $this->run_command(
          new MainCli()
        );
        break;

      case 'create_manifest':
        $this->run_command(
          new ManifestCli()
        );
        break;

      case 'create_media':
        $this->run_command(
          new MediaCli()
        );
        break;

      case 'create_menu':
        $this->run_command(
          new MenuCli()
        );
        break;

      case 'create_rest_field':
        $this->run_command(
          new FieldCli(),
          [
            'field_name'  => $args[1] ?? 'title',
            'object_type' => $args[2] ?? 'post',
          ]
        );
        break;

      case 'create_rest_route':
        $this->run_command(
          new RouteCli(),
          [
            'endpoint_slug' => $args[1] ?? 'test',
            'method'        => $args[2] ?? 'get',
          ]
        );
        break;

      default:
        \WP_CLI::error( 'First argument must be a valid command name. Your command is missing or not valid.' );
        break;
    }
  }

  /**
   * Run all CLI commands for normal WPCLI.
   *
   * @param string $command_parent_name Define top level commands name.
   *
   * @return void
   */
  public function run( string $command_parent_name ) {
    $this->command_parent_name = $command_parent_name;

    $this->run_command( new ConfigCli() );
    $this->run_command( new I18nCli() );
    $this->run_command( new MainCli() );
    $this->run_command( new ManifestCli() );
    $this->run_command( new MediaCli() );
    $this->run_command( new MenuCli() );
    $this->run_command( new RouteCli() );
    $this->run_command( new FieldCli() );
  }

  /**
   * Run simgle command depending on what type of env.
   *
   * @return void
   */
  public function run_command( $class, array $args = [] ) {
    if ( ! function_exists( 'add_action' ) ) {
      // Run if in development from library env.
      $class->__invoke(
        [],
        $args
      );
    } else {
      // Run if normal WPCLI.
      $class->register( $this->command_parent_name );
    }
  }
}
