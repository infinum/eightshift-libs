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
	 * @param array<string, mixed> $args Optional arguments.
	 *
	 * @return void
	 */
	function dbExport(string $projectRootPath, array $args = [])
	{
		// Check if optional parameters exists.
		$skipDb = $args['skip_db'] ?? false;
		$skipUploads = $args['skip_uploads'] ?? false;

		// Change execution folder.
		if (!is_dir($projectRootPath)) {
			CliHelpers::cliError("Folder doesn't exist on this path: {$projectRootPath}.");
		}

		chdir($projectRootPath);

		// Define db export file name.
		$dbFileName = 'latest.sql';

		// Define export file name.
		$exportFileName = 'latest_dump.tar.gz';

		// Define path to uploads folder.
		$uploadsFolder = 'wp-content/uploads';

		// Remove old export file if it exists.
		if (file_exists($exportFileName)) {
			unlink($exportFileName);
		}

		// Execute db export.
		if (!$skipDb) {
			WP_CLI::runcommand("db export {$dbFileName}");
			WP_CLI::log("Exported db to {$projectRootPath} folder.");

			WP_CLI::log('--------------------------------------------------');
		}

		// Execute compress and export for db and uploads folder.
		$exportFiles = "{$dbFileName} {$uploadsFolder}";

		if ($skipDb) {
			$exportFiles = "{$uploadsFolder}";

			if (!file_exists($uploadsFolder)) {
				$exportFiles = '';
			}
		}

		if ($skipUploads) {
			$exportFiles = "{$dbFileName}";
		}

		if (!empty($exportFiles)) {
			WP_CLI::log((string)shell_exec("tar czf {$exportFileName} {$exportFiles}"));
			WP_CLI::log('Compressing folders success.');
			WP_CLI::log('--------------------------------------------------');
		}

		// Finishing.
		WP_CLI::success("Export complete! File {$exportFileName} is located in {$projectRootPath} folder.");
		WP_CLI::log('--------------------------------------------------');

		// Remove old db export file if it exists.
		if (file_exists($dbFileName)) {
			unlink($dbFileName);
		}
	}
}
