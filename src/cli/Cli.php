<?php
/**
 * The class file that holds abstract class for WPCLI
 *
 * @package EightshiftLibs\Cli
 */

namespace EightshiftLibs\Cli;

use EightshiftLibs\Blocks\BlocksBlockCli;
use EightshiftLibs\Blocks\BlocksCli;
use EightshiftLibs\Blocks\BlocksComponentCli;
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
    BlocksComponentCli::class,
    BlocksBlockCli::class,
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
   * All classes and commands used for project setup.
   */
  const SETUP_CLASSES = [
    CliInitTheme::class,
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
      static::SETUP_CLASSES,
    );
  }

  /**
   * Define all classes to register for normal WP.
   *
   * @return array
   */
  public function get_public_classes() {
    return array_merge(
      static::PUBLIC_CLASSES,
      static::SETUP_CLASSES,
    );
  }

  /**
   * Run all CLI commands for develop.
   *
   * @param array $args WPCLI eval-file arguments.
   *
   * @return void
   */
  public function load_develop( array $args = [] ) : void {

    $command_name = $args[0] ?? '';

    if ( empty( $command_name ) ) {
      \WP_CLI::error( 'First argument must be a valid command name.' );
    }

    foreach ( $this->get_develop_classes() as $item ) {
      $reflection_class = new \ReflectionClass($item);
      $class            = $reflection_class->newInstanceArgs( [ null ] );

      if ( $class->get_command_name() === $command_name ) {
        $class->__invoke(
          [],
          $class->get_develop_args( $args )
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
  public function load( string $command_parent_name ) : void {
    $this->command_parent_name = $command_parent_name;

    foreach ( $this->get_public_classes() as $item ) {
      $reflection_class = new \ReflectionClass($item);
      $class            = $reflection_class->newInstanceArgs( [ $this->command_parent_name ] );

      $class->register();
    }
  }
}
