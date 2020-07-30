<?php
/**
 * The class file that holds abstract class for WPCLI
 *
 * @package EightshiftLibs\Cli
 */

namespace EightshiftLibs\Cli;

use EightshiftLibs\Config\ConfigCli;
use EightshiftLibs\CustomPostType\PostTypeCli;
use EightshiftLibs\CustomTaxonomy\TaxonomyCli;
use EightshiftLibs\I18n\I18nCli;
use EightshiftLibs\Login\LoginCli;
use EightshiftLibs\Main\MainCli;
use EightshiftLibs\Manifest\ManifestCli;
use EightshiftLibs\Media\MediaCli;
use EightshiftLibs\Menu\MenuCli;
use EightshiftLibs\ModifyAdminAppearance\ModifyAdminAppearanceCli;
use EightshiftLibs\Rest\Fields\FieldCli;
use EightshiftLibs\Rest\Routes\RouteCli;
use EightshiftLibs\Services\ServiceCli;

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
   * Define all classes to register for WPCLI.
   *
   * @return array
   */
  public function get_public_classes() {
    return [
      ConfigCli::class,
      PostTypeCli::class,
      TaxonomyCli::class,
      I18nCli::class,
      LoginCli::class,
      MainCli::class,
      ManifestCli::class,
      MediaCli::class,
      MenuCli::class,
      ModifyAdminAppearanceCli::class,
      FieldCli::class,
      RouteCli::class,
      ServiceCli::class,
    ];
  }

  /**
   * Define all classes to register for development.
   *
   * @return array
   */
  public function get_develop_classes() {
    return array_merge(
      $this->get_public_classes(),
      [
        CliReset::class,
        CliRunAll::class,
      ],
    );
  }

  /**
   * Run all CLI commands
   *
   * @param array $args WPCLI eval-file arguments.
   *
   * @return void
   */
  public function load_develop( array $args = [] ) {

    $command_name = $args[0] ?? '';

    foreach ( $this->get_develop_classes() as $item ) {
      $class_name = new $item;

      if ( $class_name->get_command_name() === $command_name ) {
        $this->run_command(
          $class_name,
          $class_name->get_develop_args( $args )
        );

        break;
      }
    }

    // \WP_CLI::error( 'First argument must be a valid command name. Your command is missing or not valid.' );
  }

  /**
   * Run all CLI commands for normal WPCLI.
   *
   * @param string $command_parent_name Define top level commands name.
   *
   * @return void
   */
  public function load( string $command_parent_name ) {
    $this->command_parent_name = $command_parent_name;

    foreach ( $this->get_public_classes() as $item ) {
      $this->run_command( new $item );
    }
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
