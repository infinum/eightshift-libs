<?php

/**
 * Script used to import database depending on the env.
 * Must provide from and to parameters that are defined in setup.json file.
 *
 * @package EightshiftLibs
 */

declare(strict_types=1);

use EightshiftLibs\Cli\CliHelpers;

if (!function_exists('dbImport')) {
	/**
	 * Importing database.
	 *
	 * @param string $setupFile Setup file path.
	 * @param array<string, mixed> $args Optional arguments.
	 *
	 * @return void
	 */
	function dbImport(string $setupFile, array $args = [])
	{
		// Check if mandatory parameters exists.
		$from = $args['from'] ?? '';
		$to = $args['to'] ?? '';

		$errorClass = new class () {
			use CliHelpers;
		};

		if (empty($from)) {
			$errorClass::cliError("--from parameter is mandatory. Please provide one url key from setup.json file.");
		}

		if (empty($to)) {
			$errorClass::cliError("--to parameter is mandatory. Please provide one url key from setup.json file.");
		}

		// Check if file exists.
		if (is_dir($setupFile) && !file_exists($setupFile)) {
			$errorClass::cliError("Setup file doesn't exist on this path: {$setupFile}.");
		}

		// Parse json file to array.
		$data = json_decode(implode(' ', (array)file($setupFile)), true);

		// Check if $data is empty.
		if (empty($data)) {
			$errorClass::cliError("Setup file is empty on this path: {$setupFile}.");
		}

		// Check if urls key exists.
		$urls = $data['urls'] ?? [];

		if (empty($urls)) {
			$errorClass::cliError('Urls key is missing or empty.');
		}

		$fromHost = '';
		$fromScheme = '';

		// Die if from key is missing and not valid.
		if (!isset($urls[$from]) || empty($urls[$from])) {
			$errorClass::cliError("{$from} key is missing or empty in urls.");
		} else {
			$from = wp_parse_url($urls[$from]);
			$fromHost = $from['host'];
			$fromScheme = $from['scheme'];
		}

		$toHost = '';
		$toScheme = '';

		// Die if to key is missing and not valid.
		if (!isset($urls[$to]) || empty($urls[$to])) {
			$errorClass::cliError("{$to} key is missing or empty in urls.");
		} else {
			$to = wp_parse_url($urls[$to]);
			$toHost = $to['host'];
			$toScheme = $to['scheme'];
		}

		if (!getenv('ES_TEST')) {
			// Define db export file name.
			$dbFileName = 'latest.sql';

			// Define export file name.
			$exportFileName = 'latest_dump.tar.gz';

			// Define export folder name.
			$exportFolderName = 'latest_dump';

			// Remove old db export folder if it exists.
			if (file_exists($exportFolderName)) {
				WP_CLI::log((string)shell_exec("rm -rf {$exportFolderName}"));
				WP_CLI::log("Removed old temp {$exportFolderName} folder.");
				WP_CLI::log('--------------------------------------------------');
			}

			// Create new temp folder.
			mkdir($exportFolderName);
			WP_CLI::log("Created temp {$exportFolderName} folder.");
			WP_CLI::log('--------------------------------------------------');

			// Export files to new temp folder.
			WP_CLI::log((string)shell_exec("tar zxf {$exportFileName} -C {$exportFolderName}"));
			WP_CLI::log("Exported {$exportFileName} to {$exportFolderName} folder.");
			WP_CLI::log('--------------------------------------------------');

			// Execute db export.
			WP_CLI::runcommand('db export');
			WP_CLI::log('Db exported successfully.');
			WP_CLI::log('--------------------------------------------------');

			WP_CLI::runcommand('db reset');
			WP_CLI::log('--------------------------------------------------');

			// Import new database.
			WP_CLI::runcommand("db import {$exportFolderName}/{$dbFileName}");
			WP_CLI::log('Database import done.');
			WP_CLI::log('--------------------------------------------------');

			// Search and replace url host.
			WP_CLI::runcommand("search-replace {$fromHost} {$toHost} --url={$fromHost} --all-tables --network");
			WP_CLI::log('Database search replace for host successfully finished.');
			WP_CLI::log('--------------------------------------------------');

			// Search and replace url scheme.
			if ($toScheme !== $fromScheme) {
				WP_CLI::runcommand("search-replace {$fromScheme}://{$toHost} {$toScheme}://{$toHost} --all-tables --network");
				WP_CLI::log('Database search replace for scheme successfully finished.');
				WP_CLI::log('--------------------------------------------------');
			}

			// Clean up.
			WP_CLI::runcommand('cache flush');
			WP_CLI::runcommand('transient delete --all');
			WP_CLI::runcommand('rewrite flush');
			WP_CLI::log('Flushing cache, removing transients and resetting permalinks!');
			WP_CLI::log('--------------------------------------------------');
		}

		WP_CLI::success('Finished! Success!');
	}
}
