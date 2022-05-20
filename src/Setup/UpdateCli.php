<?php

/**
 * Class that registers WPCLI command for Setup.
 *
 * @package EightshiftLibs\Setup
 */

declare(strict_types=1);

namespace EightshiftLibs\Setup;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\CliHelpers;
use WP_CLI;
use WP_CLI\ExitException;

/**
 * Class UpdateCli
 */
class UpdateCli extends AbstractCli
{
	/**
	 * Get WPCLI command parent name
	 *
	 * @return string
	 */
	public function getCommandParentName(): string
	{
		return 'run';
	}

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'update';
	}

	/**
	 * Get WPCLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Run project update with details stored in setup.json file.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'skip_core',
					'description' => 'If you want to skip core update/installation, provide bool on this attr.',
					'optional' => true,
				],
				[
					'type' => 'assoc',
					'name' => 'skip_plugins',
					'description' => 'If you want to skip all plugins update/installation, provide bool on this attr.',
					'optional' => true,
				],
				[
					'type' => 'assoc',
					'name' => 'skip_plugins_core',
					'description' => 'If you want to skip plugins only from core update/installation, provide bool on this attr.',
					'optional' => true,
				],
				[
					'type' => 'assoc',
					'name' => 'skip_plugins_github',
					'description' => 'If you want to skip plugins only from github update/installation, provide bool on this attr.',
					'optional' => true,
				],
				[
					'type' => 'assoc',
					'name' => 'skip_themes',
					'description' => 'If you want to skip themes update/installation, provide bool on this attr.',
					'optional' => true,
				],
			],
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{

		$setupFilename = 'setup.json';

		if (\getenv('ES_TEST') !== false) {
			$setupFilename = $this->getProjectConfigRootPath() . '/cliOutput/setup.json';
		}

		try {
			$this->setup(
				$this->getProjectConfigRootPath(),
				[
					'skip_core' => $assocArgs['skip_core'] ?? false,
					'skip_plugins' => $assocArgs['skip_plugins'] ?? false,
					'skip_plugins_core' => $assocArgs['skip_plugins_core'] ?? false,
					'skip_plugins_github' => $assocArgs['skip_plugins_github'] ?? false,
					'skip_themes' => $assocArgs['skip_themes'] ?? false,
				],
				$setupFilename
			);
		} catch (ExitException $e) {
			exit("{$e->getCode()}: {$e->getMessage()}"); // phpcs:ignore Eightshift.Security.ComponentsEscape.OutputNotEscaped
		}
	}


	/**
	 * Update project and setup all plugins, themes and core
	 *
	 * @param string $projectRootPath Root of the project where config is located.
	 * @param array<string, mixed>  $args Optional arguments.
	 * @param string $setupFile Define setup file name.
	 *
	 * @return void
	 */
	private function setup(string $projectRootPath, array $args = [], string $setupFile = 'setup.json')
	{
		// Check if optional parameters exists.
		$skipCore = $args['skip_core'] ?? false;
		$skipPlugins = $args['skip_plugins'] ?? false;
		$skipPluginsCore = $args['skip_plugins_core'] ?? false;
		$skipPluginsGithub = $args['skip_plugins_github'] ?? false;
		$skipThemes = $args['skip_themes'] ?? false;

		// Change execution folder.
		if (!\is_dir($projectRootPath)) {
			CliHelpers::cliError("Folder doesn't exist on this path: {$projectRootPath}.");
		}

		\chdir($projectRootPath);

		// Check if setup exists.
		if (!\file_exists($setupFile)) {
			CliHelpers::cliError("setup.json is missing at this path: {$setupFile}.");
		}

		// Parse json file to array.
		$data = \json_decode(\implode(' ', (array)\file($setupFile)), true);

		if (empty($data)) {
			CliHelpers::cliError("{$setupFile} is empty.");
		}

		// Check if core key exists in config.
		if (!$skipCore) {
			$core = $data['core'] ?? '';

			// Install core version.
			if (!empty($core)) {
				WP_CLI::runcommand("core update --version={$core} --force");
				WP_CLI::log('--------------------------------------------------');
			} else {
				WP_CLI::warning('No core version is defined. Skipping.');
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
							WP_CLI::runcommand("plugin install {$name} --version={$version} --force");
							WP_CLI::log('--------------------------------------------------');
						}
					} else {
						WP_CLI::warning('No core plugins are defined. Skipping.');
					}
				}

				if (!$skipPluginsGithub) {
					// Check if plugins github key exists in config.
					$pluginsGithub = $plugins['github'] ?? [];

					// Install github plugins.
					if (!empty($pluginsGithub)) {
						foreach ($pluginsGithub as $name => $version) {
							$shortName = CliHelpers::getGithubPluginName($name);
							$filePath = \getcwd() . "/{$shortName}.zip";
							$releaseZip = \file_get_contents("https://github.com/{$name}/releases/download/{$version}/release.zip"); // phpcs:ignore WordPress.WP.AlternativeFunctions
							\file_put_contents($filePath, $releaseZip); // phpcs:ignore WordPress.WP.AlternativeFunctions
							WP_CLI::runcommand("plugin install {$filePath} --force");
							WP_CLI::log('--------------------------------------------------');
							\unlink($filePath);
						}
					} else {
						WP_CLI::warning('No Github plugins are defined. Skipping.');
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
					WP_CLI::runcommand("theme install {$name} --version={$version} --force");
					WP_CLI::log('--------------------------------------------------');
				}
			} else {
				WP_CLI::warning('No themes are defined. Skipping.');
			}
		}

		WP_CLI::success('All commands are finished.');
		WP_CLI::log('--------------------------------------------------');
	}
}
