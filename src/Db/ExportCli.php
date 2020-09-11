<?php
/**
 * Class that registers WPCLI command for Export.
 *
 * @package EightshiftLibs\Db
 */

declare( strict_types=1 );

namespace EightshiftLibs\Db;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class ExportCli
 */
class ExportCli extends AbstractCli {

  /**
   * Get WPCLI command name
   *
   * @return string
   */
  public function get_command_name() : string {
    return 'run_export';
  }

  /**
   * Get WPCLI command doc.
   *
   * @return string
   */
  public function get_doc() : array {
    return [
      'shortdesc' => 'Run database export with images.',
      'synopsis' => [
        [
          'type'        => 'assoc',
          'name'        => 'skip_db',
          'description' => 'If you want to skip exporting database.',
          'optional'    => true,
        ],
        [
          'type'        => 'assoc',
          'name'        => 'skip_uploads',
          'description' => 'If you want to skip exporting images.',
          'optional'    => true,
        ],
      ],
    ];
  }

  public function __invoke( array $args, array $assoc_args ) { // phpcs:ignore Squiz.Commenting.FunctionComment.Missing, Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClassBeforeLastUsed

    require $this->get_libs_path( 'src/Db/DbExport.php' );

    db_export(
      $this->get_project_config_root_path(),
      [
        'skip_db'      => $assoc_args['skip_db'] ?? false,
        'skip_uploads' => $assoc_args['skip_uploads'] ?? false,
      ]
    );
  }
}
