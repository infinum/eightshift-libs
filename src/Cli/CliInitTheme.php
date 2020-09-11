<?php
/**
 * Class that registers WPCLI command initial setup of theme project.
 *
 * @package EightshiftLibs\Cli
 */

declare( strict_types=1 );

namespace EightshiftLibs\Cli;

use EightshiftLibs\Blocks\BlocksCli;
use EightshiftLibs\Build\BuildCli;
use EightshiftLibs\CiExclude\CiExcludeCli;
use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Config\ConfigCli;
use EightshiftLibs\Enqueue\Admin\EnqueueAdminCli;
use EightshiftLibs\Enqueue\Blocks\EnqueueBlocksCli;
use EightshiftLibs\Enqueue\Theme\EnqueueThemeCli;
use EightshiftLibs\GitIgnore\GitIgnoreCli;
use EightshiftLibs\LintPhp\LintPhpCli;
use EightshiftLibs\Main\MainCli;
use EightshiftLibs\Manifest\ManifestCli;
use EightshiftLibs\Menu\MenuCli;
use EightshiftLibs\Setup\SetupCli;

/**
 * Class CliInitTheme
 */
class CliInitTheme extends AbstractCli {

  /**
   * All classes for initial theme setup for project.
   */
  const INIT_THEME_CLASSES = [
    BlocksCli::class,
    EnqueueAdminCli::class,
    EnqueueBlocksCli::class,
    EnqueueThemeCli::class,
    ConfigCli::class,
    MainCli::class,
    ManifestCli::class,
    MenuCli::class,
    BuildCli::class,
    LintPhpCli::class,
    GitIgnoreCli::class,
    SetupCli::class,
    CiExcludeCli::class,
  ];

  /**
   * Get WPCLI command name
   *
   * @return string
   */
  public function get_command_name() : string {
    return 'init_theme';
  }

  /**
   * Get WPCLI command doc.
   *
   * @return string
   */
  public function get_doc() : array {
    return [
      'shortdesc' => 'Generates initial setup for WordPress theme project.',
    ];
  }

  public function __invoke( array $args, array $assoc_args ) { // phpcs:ignore Squiz.Commenting.FunctionComment.Missing, Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClassBeforeLastUsed

    if ( ! function_exists( 'add_action' ) ) {
      $this->run_reset();
      \WP_CLI::log( '--------------------------------------------------' );
    }

    foreach ( static::INIT_THEME_CLASSES as $item ) {
      $reflection_class = new \ReflectionClass( $item );
      $class            = $reflection_class->newInstanceArgs( [ null ] );

      if ( function_exists( 'add_action' ) ) {
        \WP_CLI::runcommand( "{$this->command_parent_name} {$class->get_command_name()}" );
      } else {
        \WP_CLI::runcommand( "eval-file bin/cli.php {$class->get_command_name()} --skip-wordpress" );
      }
    }

    \WP_CLI::log( '--------------------------------------------------' );

    \WP_CLI::success( 'All commands are finished.' );
  }
}
