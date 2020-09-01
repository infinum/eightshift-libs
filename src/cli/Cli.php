<?php
/**
 * The class file that holds abstract class for WPCLI
 *
 * @package EightshiftLibs\Cli
 */

namespace EightshiftLibs\Cli;

use EightshiftLibs\Blocks\BlocksCli;
use EightshiftLibs\Config\ConfigCli;
use EightshiftLibs\CustomPostType\PostTypeCli;
use EightshiftLibs\CustomTaxonomy\TaxonomyCli;
use EightshiftLibs\Enqueue\Admin\EnqueueAdminCli;
use EightshiftLibs\Enqueue\Blocks\EnqueueBlocksCli;
use EightshiftLibs\Enqueue\Theme\EnqueueThemeCli;
use EightshiftLibs\ExampleService\ServiceExampleCli;
use EightshiftLibs\I18n\I18nCli;
use EightshiftLibs\Login\LoginCli;
use EightshiftLibs\Main\MainCli;
use EightshiftLibs\Manifest\ManifestCli;
use EightshiftLibs\Media\MediaCli;
use EightshiftLibs\Menu\MenuCli;
use EightshiftLibs\ModifyAdminAppearance\ModifyAdminAppearanceCli;
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
   * All classes and commands used only for WPCLI.
   */
  const PUBLIC_CLASSES = [
    BlocksCli::class,
    EnqueueAdminCli::class,
    EnqueueBlocksCli::class,
    EnqueueThemeCli::class,
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
    ServiceExampleCli::class,
  ];

  /**
   * All classes and commands used only for development.
   */
  CONST DEVELOP_CLASSES = [
    CliReset::class,
    CliRunAll::class,
    CliShowAll::class,
  ];

  /**
   * Define all classes to register for development.
   *
   * @return array
   */
  public function get_develop_classes() {
    return array_merge(
      static::PUBLIC_CLASSES,
      static::DEVELOP_CLASSES,
    );
  }

  /**
   * Run all CLI commands for develop.
   *
   * @param array $args WPCLI eval-file arguments.
   *
   * @return void
   */
  public function load_develop( array $args = [] ) {

    $command_name = $args[0] ?? '';

    if ( empty( $command_name ) ) {
      \WP_CLI::error( 'First argument must be a valid command name.' );
    }

    foreach ( $this->get_develop_classes() as $item ) {
      $class_name = new $item;

      if ( $class_name->get_command_name() === $command_name ) {
        $class_name->__invoke(
          [],
          $class_name->get_develop_args( $args )
        );

        break;
      }
    }
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

    foreach ( static::PUBLIC_CLASSES as $item ) {
      (new $item)->register( $this->command_parent_name );
    }
  }
}
