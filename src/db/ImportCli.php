<?php
/**
 * Class that registers WPCLI command for Import.
 *
 * @package EightshiftLibs\Db
 */

declare( strict_types=1 );

namespace EightshiftLibs\Db;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class ImportCli
 */
class ImportCli extends AbstractCli {

  /**
   * Get WPCLI command name
   *
   * @return string
   */
  public function get_command_name() : string {
    return 'run_import';
  }

  /**
   * Get WPCLI command doc.
   *
   * @return string
   */
  public function get_doc() : array {
    return [
      'shortdesc' => 'Run database import based on enviroments.',
      'synopsis' => [
        [
          'type'        => 'assoc',
          'name'        => 'from',
          'description' => 'Set from what enviroment you have exported the data.',
          'optional'    => true,
        ],
        [
          'type'        => 'assoc',
          'name'        => 'to',
          'description' => 'Set to what enviroment you want to import the data.',
          'optional'    => true,
        ],
      ],
    ];
  }

  public function __invoke( array $args, array $assoc_args ) { // phpcs:ignore Squiz.Commenting.FunctionComment.Missing, Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClassBeforeLastUsed

    require $this->get_libs_path( 'src/Db/DbImport.php' );

    db_import(
      $this->get_project_config_root_path(),
      [
        'from' => $assoc_args['from'] ?? '',
        'to'   => $assoc_args['to'] ?? '',
      ]
    );
  }
}
