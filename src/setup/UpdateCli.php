<?php
/**
 * Class that registers WPCLI command for Setup.
 *
 * @package EightshiftLibs\Setup
 */

namespace EightshiftLibs\Setup;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class UpdateCli
 */
class UpdateCli extends AbstractCli {

  /**
   * Get WPCLI command name
   *
   * @return string
   */
  public function get_command_name() : string {
    return 'update';
  }

  /**
   * Get WPCLI command doc.
   *
   * @return string
   */
  public function get_doc() : array {
    return [
      'shortdesc' => 'Run project update with detailes stored in setup.json file.',
      'synopsis' => [
        [
          'type'        => 'assoc',
          'name'        => 'skip_core',
          'description' => 'If you want to skip core update/instalation provide bool on this attr.',
          'optional'    => true,
        ],
        [
          'type'        => 'assoc',
          'name'        => 'skip_plugins',
          'description' => 'If you want to skip all plugins update/instalation provide bool on this attr.',
          'optional'    => true,
        ],
        [
          'type'        => 'assoc',
          'name'        => 'skip_plugins_core',
          'description' => 'If you want to skip plugins only from core update/instalation provide bool on this attr.',
          'optional'    => true,
        ],
        [
          'type'        => 'assoc',
          'name'        => 'skip_plugins_github',
          'description' => 'If you want to skip plugins only from github update/instalation provide bool on this attr.',
          'optional'    => true,
        ],
        [
          'type'        => 'assoc',
          'name'        => 'skip_themes',
          'description' => 'If you want to skip themes update/instalation provide bool on this attr.',
          'optional'    => true,
        ],
      ],
    ];
  }

  public function __invoke( array $args, array $assoc_args ) {

    require $this->get_libs_path( 'src/setup/Setup.php' );

    setup(
      $this->get_project_config_root_path(),
      [
        'skip_core'           => $assoc_args['skip_core'] ?? false,
        'skip_plugins'        => $assoc_args['skip_plugins'] ?? false,
        'skip_plugins_core'   => $assoc_args['skip_plugins_core'] ?? false,
        'skip_plugins_github' => $assoc_args['skip_plugins_github'] ?? false,
        'skip_themes'         => $assoc_args['skip_themes'] ?? false,
      ]
    );
  }
}
