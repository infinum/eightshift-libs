<?php

/**
 * Class that registers WPCLI command for plugin install/update.
 *
 * @package EightshiftLibs\Setup
 */

declare(strict_types=1);

namespace EightshiftLibs\Setup;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliRun;
use EightshiftLibs\Exception\FileMissing;
use WP_CLI;

/**
 * Class PluginInstallCli
 */
class PluginInstallCli extends AbstractCli
{
	/**
	 * Get WPCLI command parent name
	 *
	 * @return string
	 */
	public function getCommandParentName(): string
	{
		return CliRun::COMMAND_NAME;
	}

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'plugin_install';
	}

	/**
	 * Define default arguments.
	 *
	 * By default only the 'core' plugins (from wordpress.org)
	 * will be installed if the command is run without any arguments
	 *
	 * @return array<string, int|string|boolean>
	 */
	public function getDefaultArgs(): array
	{
		return [
			'install-all' => 'false',
			'install-core' => 'false',
			'install-github' => 'false',
			'install-paid' => 'false',
			'install-folder' => 'false',
		];
	}

	/**
	 * Get WPCLI command doc
	 *
	 * @return array<string, mixed>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Install or update the WordPress plugins based on the setup.json file.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'install-all',
					'description' => 'If you want to install all the plugins from the setup.json file.',
					'optional' => true,
					'default' => $this->getDefaultArg('install-all'),
					'options' => [
						'true',
						'false',
					],
				],
				[
					'type' => 'assoc',
					'name' => 'install-core',
					'description' => 'If you want to install only the wordpress.org plugins. This is the default option.',
					'optional' => true,
					'default' => $this->getDefaultArg('install-core'),
					'options' => [
						'true',
						'false',
					],
				],
				[
					'type' => 'assoc',
					'name' => 'install-github',
					'description' => 'If you want to install only the plugins from github.org.',
					'optional' => true,
					'default' => $this->getDefaultArg('install-github'),
					'options' => [
						'true',
						'false',
					],
				],
				[
					'type' => 'assoc',
					'name' => 'install-paid',
					'description' => 'If you want to install only the paid plugins plugins.
					You\'ll need an additional env.json file with premium plugin URLs.',
					'optional' => true,
					'default' => $this->getDefaultArg('install-paid'),
					'options' => [
						'true',
						'false',
					],
				],
				[
					'type' => 'assoc',
					'name' => 'install-folder',
					'description' => 'If you want to install only the plugins committed to the repository.',
					'optional' => true,
					'default' => $this->getDefaultArg('install-folder'),
					'options' => [
						'true',
						'false',
					],
				],
			],
			'longdesc' => $this->prepareLongDesc(
				"
				## USAGE

				This command will install or update the plugins based on the setup.json file.
				
				In order to install the premium plugins you will need to have an env.json file which
				is usually stored in a secret vault. That file should looks something like this:
				
				{
					\"advanced-custom-fields-pro\": \"url==&t=VERSION\",
					\"wp-rocket\": \"url\",
				}
				
				If the URl contains the VERSION string, that version will be replaced with the version
				defined in the setup.json file. Otherwise the latest version available from the URL will
				be downloaded.

				## EXAMPLES

				# Install only the wp.org plugins:
				$ wp boilerplate {$this->getCommandParentName()} {$this->getCommandName()}

				# Update all the plugins:
				$ wp boilerplate {$this->getCommandParentName()} {$this->getCommandName()} --install-all'
				
				# Install only the paid plugins:
				$ wp boilerplate {$this->getCommandParentName()} {$this->getCommandName()} --install-paid'
			"
			),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		/**
		 * Check associated arguments. Based on which one is present
		 * toggle the behavior of the CLI command.
		 */
		if (\in_array('install-core', $assocArgs)) {
			$corePluginInstall = true;
		}

		if (\in_array('install-github', $assocArgs)) {
			$githubPluginInstall = true;
		}

		if (\in_array('install-paid', $assocArgs)) {
			$paidPluginInstall = true;
		}

		if (\in_array('install-folder', $assocArgs)) {
			$folderPluginInstall = true;
		}

		$setupFile = $this->getProjectConfigRootPath() . '/setup.json';

		if (\getenv('ES_TEST') === '1') {
			$setupFile = dirname(__FILE__) . '/setup.json';
		}

		if (!\file_exists($setupFile)) {
			throw FileMissing::missingFileOnPath($setupFile);
		}

		$cliOptions = [
			'return' => true,
			'parse' => 'json',
			'launch' => false,
			'exit_error' => true,
		];

		$setup = json_decode((string)\file_get_contents($setupFile), true);

		$coreSetupPlugins = $setup['plugins']['core'] ?? [];
		$ghSetupPlugins = $setup['plugins']['github'] ?? [];
		$paidSetupPlugins = $setup['plugins']['paid'] ?? [];
		$folderSetupPlugins = $setup['plugins']['folder'] ?? [];

		// Get installed plugins.
		$installedPluginsList = WP_CLI::runcommand('plugin list --field=name --format=json', $cliOptions);

		$installedPlugins = json_decode($installedPluginsList, true);

		$ghPlugins = [];
		$ghPluginsRepo = [];
		if (!empty($ghSetupPlugins)) {
			// We need to replace the package notifier from GH, because wp plugin list returns just the plugin name.
			// But we also need to have some pointer about the package.
			foreach ($ghSetupPlugins as $ghPluginName => $ghPluginVersion) {
				$cleanedUpName = str_replace('/', '', strstr($ghPluginName, '/'));

				$ghPlugins[$cleanedUpName] = $ghPluginVersion;
				$ghPluginsRepo[$cleanedUpName] = $ghPluginName;
			}
		}

		$gitHubPlugins = array_keys($ghPlugins); // Get just the names of the plugins.
		$paidPlugins = array_keys($paidSetupPlugins); // Get just the names of the plugins.
		$folderPlugins = array_keys($folderSetupPlugins); // Get just the names of the plugins.

		$setupPluginList = array_merge(
			array_keys($coreSetupPlugins),
			$gitHubPlugins,
			$paidPlugins,
			$folderPlugins
		);

		$pluginsToAdd = array_diff($setupPluginList, $installedPlugins);
		$pluginsToRemove = array_diff($installedPlugins, $setupPluginList);

		WP_CLI::log('Check plugins');

		foreach ($pluginsToRemove as $pluginToRemove) {
			WP_CLI::runcommand("plugin delete {$pluginToRemove}", $cliOptions);
			WP_CLI::log("Plugin {$pluginToRemove} removed");
		}

		foreach ($pluginsToAdd as $pluginToAdd) {
			if (isset(array_flip($gitHubPlugins)[$pluginToAdd])) {
				$version = $ghPlugins[$pluginToAdd];
				$repoName = $ghPluginsRepo[$pluginToAdd];

				$this->installGHPlugin($repoName, $version);
			} elseif (isset(array_flip($paidPlugins)[$pluginToAdd])) {
				$version = $paidSetupPlugins[$pluginToAdd];

				$this->installPaidPlugin($pluginToAdd, $version);
			} elseif (isset(array_flip($folderPlugins)[$pluginToAdd])) {
				continue;
			} else {
				WP_CLI::runcommand("plugin install {$pluginToAdd} --force", $cliOptions);
			}

			WP_CLI::log("Plugin {$pluginToAdd} installed");
		}

		// Check plugin versions and update if needed.
		$currentlyInstalledPlugins = WP_CLI::runcommand('plugin list --fields=name,version --format=json', $cliOptions);
		$currentlyInstalledPlugins = json_decode($currentlyInstalledPlugins, true);

		$currentVersions = [];

		foreach ($currentlyInstalledPlugins as $pluginDetails) {
			$currentVersions[$pluginDetails['name']] = $pluginDetails['version'];
		}

		$setupPluginVersions = array_merge($coreSetupPlugins, $ghPlugins, $paidSetupPlugins, $folderSetupPlugins);

		// Compare versions of the two arrays key for key.
		foreach ($setupPluginVersions as $pluginName => $setupPluginVersion) {
			// Update if the current version of the plugin is greater or less than the setup version.
			if ($currentVersions[$pluginName] === $setupPluginVersion) {
				continue;
			}

			// Check if plugin is from GH or not.
			if (isset($ghPlugins[$pluginName])) {
				// The only way to update is to reinstall.
				$repoName = $ghPluginsRepo[$pluginName];

				$this->installGHPlugin($repoName, $setupPluginVersion);
			} elseif (isset($paidSetupPlugins[$pluginName])) {
				$this->installPaidPlugin($pluginName, $setupPluginVersion);
			} else {
				WP_CLI::runcommand("plugin update {$pluginName} --version={$setupPluginVersion}");
			}

			WP_CLI::log("Plugin {$pluginName} updated");
		}
	}

	/**
	 * Extract the GitHub plugin name from the identifier
	 *
	 * @param string $name Identifier of the plugin from the setup.json file.
	 *
	 * @return string Plugin slug.
	 */
	private function getGithubPluginName(string $name): string
	{
		// If the plugin doesn't have a namespace, we're good, just return it.
		if (\strpos($name, '/') === false) {
			return $name;
		}

		$splitName = explode('/', $name);

		return $splitName[count($splitName) - 1];
	}

	/**
	 * Helper used to install the plugin from GitHub
	 *
	 * Will install the plugin, and rename the folder so it's not hashed.
	 *
	 * @param string $name Plugin slug.
	 * @param string $version Plugin version number.
	 */
	private function installGHPlugin(string $name, string $version) {
		$shortName = $this->getGithubPluginName($name);

		WP_CLI::runcommand("plugin install \"https://github.com/{$name}/releases/download/{$version}/release.zip\" --force");
		WP_CLI::runcommand("for f in ./wp-content/plugins/release*/; do rsync -avh --delete \"\$f\" \"./wp-content/plugins/{$shortName}/\" && rm -rf \"\$f\"; done"); // Rename the plugin folder.
	}

	/**
	 * Helper to install paid plugins
	 *
	 * If the URL of the paid plugin has a version placeholder
	 * will replace the placeholder with the version set in the setup.json.
	 *
	 * @param string $name Plugin slug.
	 * @param string $version Plugin version.
	 */
	private function installPaidPlugin(string $name, string $version) {
		// Check if env.json exist.
		$envFile = $this->getProjectConfigRootPath() . '/env.json';

		if (\getenv('ES_TEST') === '1') {
			$envFile = dirname(__DIR__, 2) . '/tests/data/env.json';
		}

		if (!\file_exists($envFile)) {
			throw FileMissing::missingFileOnPath($envFile);
		}

		$envData = json_decode((string)\file_get_contents($envFile), true);

		$plugin = $envData[$name];

		if (strpos($plugin, 'VERSION') !== false) {
			$pluginUrl = str_replace('VERSION', $version, $plugin);
		} else {
			$pluginUrl = $plugin;
		}

		// Install plugin.
		WP_CLI::runcommand("plugin install \"{$pluginUrl}\" --force");
	}
}
