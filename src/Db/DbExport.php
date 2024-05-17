<?php

/**
 * Script used to export database and images to zip.
 *
 * @package EightshiftLibs
 */

declare(strict_types=1);

use EightshiftLibs\Cli\CliHelpers;

if (!function_exists('dbExport')) {
	/**
	 * Exporting database.
	 *
	 * @param string $projectRootPath Root of the project where config is located.
	 *
	 * @return void
	 */
	function dbExport(string $projectRootPath)
	{
		// Change execution folder.
		if (!is_dir($projectRootPath)) {
			$errorClass = new class () {
				use CliHelpers;
			};

			$errorClass::cliError("Folder doesn't exist on this path: {$projectRootPath}.");
		}

		chdir($projectRootPath);

		WP_CLI::runcommand("db export --set-gtid-purged=OFF");

		// Finishing.
		WP_CLI::success("Export complete! DB export file is located in `{$projectRootPath}` folder.");
		WP_CLI::log('--------------------------------------------------');
	}
}
