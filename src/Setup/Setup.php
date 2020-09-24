<?php

/**
 * Script used to run project setup and installing all plugins, themes and core.
 *
 * @package EightshiftLibs
 */

declare(strict_types=1);

/**
 * Update project and setup all plugins, themes and core
 *
 * @param string $projectRootPath Root of the project where config is located.
 * @param array  $args Optional arguments.
 * @param string $setupFile Define setup file name.
 *
 * @throws \WP_CLI\ExitException WP-CLI Exception.
 *
 * @return void
 */
function setup(string $projectRootPath, array $args = [], string $setupFile = 'setup.json')
{
	// Check if optional parameters exists.
	$skipCore = $args['skip_core'] ?? false;
	$skipPlugins = $args['skip_plugins'] ?? false;
	$skipPluginsCore = $args['skip_plugins_core'] ?? false;
	$skipPluginsGithub = $args['skip_plugins_github'] ?? false;
	$skipThemes = $args['skip_themes'] ?? false;

	// Change execution folder.
	if (!is_dir($projectRootPath)) {
		\WP_CLI::error("Folder doesn't exist on this path: {$projectRootPath}.");
	}

	chdir($projectRootPath);

	// Check if setup exists.
	if (!file_exists($setupFile)) {
		\WP_CLI::error("setup.json is missing at this path: {$setupFile}.");
	}

	// Parse json file to array.
	$data = json_decode(implode(' ', (array)file($setupFile)), true);

	if (empty($data)) {
		\WP_CLI::error("{$setupFile} is empty.");
	}

	// Check if core key exists in config.
	if (!$skipCore) {
		$core = $data['core'] ?? '';

		// Install core version.
		if (!empty($core)) {
			\WP_CLI::runcommand("core update --version={$core} --force");
			\WP_CLI::log('--------------------------------------------------');
		} else {
			\WP_CLI::warning('No core version is defined. Skipping.');
		}
	}

	// Check if plugins key exists in config.
	if (!$skipPlugins) {
		$plugins = $data['plugins'] ?? [];

		if (!empty($plugins)) {
			if (!$skipPluginsCore) {
				// Check if plugins core key exists in config.
				$pluginsCore = $plugins['core'] ?? [];

				// Install core plugins.
				if (!empty($pluginsCore)) {
					foreach ($pluginsCore as $name => $version) {
						\WP_CLI::runcommand("plugin install {$name} --version={$version} --force");
						\WP_CLI::log('--------------------------------------------------');
					}
				} else {
					\WP_CLI::warning('No core plugins are defined. Skipping.');
				}
			}

			if (!$skipPluginsGithub) {
				// Check if plugins github key exists in config.
				$pluginsGithub = $plugins['github'] ?? [];

				// Install github plugins.
				if (!empty($pluginsGithub)) {
					foreach ($pluginsGithub as $name => $version) {
						\WP_CLI::runcommand("plugin install https://github.com/{$name}/archive/{$version}.zip --force");
						\WP_CLI::log('--------------------------------------------------');
					}
				} else {
					\WP_CLI::warning('No Github plugins are defined. Skipping.');
				}
			}
		}
	}

	// Check if themes key exists in config.
	if (!$skipThemes) {
		$themes = $data['themes'] ?? [];

		// Install themes.
		if (!empty($themes)) {
			foreach ($themes as $name => $version) {
				\WP_CLI::runcommand("theme install {$name} --version={$version} --force");
				\WP_CLI::log('--------------------------------------------------');
			}
		} else {
			\WP_CLI::warning('No themes are defined. Skipping.');
		}
	}

	\WP_CLI::success('All commands are finished.');
	\WP_CLI::log('--------------------------------------------------');
}
