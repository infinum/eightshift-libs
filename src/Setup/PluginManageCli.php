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
 * Class PluginManageCli
 */
class PluginManageCli extends AbstractCli
{

	/**
	 * Default CLI options
	 *
	 * @var array<string, string|boolean>
	 */
	private $cliOptions = [
		'return' => true,
		'parse' => 'json',
		'launch' => false,
		'exit_error' => true,
	];

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
		return 'plugin_manage';
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
			'delete-plugins' => 'false',
			'install-core' => 'false',
			'install-github' => 'false',
			'install-paid' => 'false',
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
					'name' => 'delete-plugins',
					'description' => 'If you want to delete plugins that are not in the setup.json list.',
					'optional' => true,
					'default' => $this->getDefaultArg('install-core'),
					'options' => [
						'true',
						'false',
					],
				],
				[
					'type' => 'assoc',
					'name' => 'install-core',
					'description' => 'If you want to install only the wordpress.org plugins.',
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
			],
			'longdesc' => $this->prepareLongDesc(
				"
				## USAGE

				This command will install, delete or update the plugins based on the setup.json file.
				
				In order to install the premium plugins you will need to have an env.json file which
				is usually stored in a secret vault. That file should looks something like this:
				
				{
					\"advanced-custom-fields-pro\": \"url==&t=VERSION\",
					\"wp-rocket\": \"url\",
				}
				
				If the URl contains the VERSION string, that version will be replaced with the version
				defined in the setup.json file. Otherwise the latest version available from the URL will be downloaded.

				## EXAMPLES

				# Install/update all the plugins, delete unused plugins:
				$ wp boilerplate {$this->getCommandParentName()} {$this->getCommandName()}

				# Install/update only the wp.org plugins:
				$ wp boilerplate {$this->getCommandParentName()} {$this->getCommandName()} --install-core'
				
				# Install/update only the paid plugins:
				$ wp boilerplate {$this->getCommandParentName()} {$this->getCommandName()} --install-paid'
				
				# Delete plugins not in the setup.json list:
				$ wp boilerplate {$this->getCommandParentName()} {$this->getCommandName()} --delete-plugins'
			"
			),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		$setup = $this->getSetupFile();

		/**
		 * Check associated arguments. Based on which one is present
		 * toggle the behavior of the CLI command.
		 */
		if (\in_array('install-core', $assocArgs)) {
			$this->installWpOrgPlugins($setup);
			return;
		}

		if (\in_array('install-github', $assocArgs)) {
			$this->installGitHubPlugins($setup);
			return;
		}

		if (\in_array('install-paid', $assocArgs)) {
			$this->installPaidPlugins($setup);
			return;
		}

		if (\in_array('delete-plugins', $assocArgs)) {
			$this->deletePlugins($setup);
			return;
		}

		$this->manageAllPlugins($setup);
	}

	private function installWpOrgPlugins(array $setup)
	{
		$coreSetupPlugins = $setup['plugins']['core'] ?? [];

		if (empty($coreSetupPlugins)) {
			WP_CLI::log('There are no wordpress.org plugins to install.');
			return;
		}

		// We only need a list of plugin names, so we'll filter the above return value.
		$installedPlugins = array_map(function($pluginElement){
			return $pluginElement['name'];
		}, $this->getCurrentlyInstalledPlugins());

		$pluginsToAdd = array_diff(array_keys($coreSetupPlugins), $installedPlugins);

		foreach ($pluginsToAdd as $pluginToAdd) {
			WP_CLI::runcommand("plugin install {$pluginToAdd} --force", $this->cliOptions);
			WP_CLI::success("Plugin {$pluginToAdd} installed");
		}

		return;
	}

	private function installGitHubPlugins(array $setup)
	{
		$ghSetupPlugins = $setup['plugins']['github'] ?? [];

		if (empty($ghSetupPlugins)) {
			WP_CLI::log('There are no GitHub plugins to install.');
			return;
		}

		// We only need a list of plugin names, so we'll filter the above return value.
		$installedPlugins = array_map(function($pluginElement){
			return $pluginElement['name'];
		}, $this->getCurrentlyInstalledPlugins());

		list($ghPlugins, $ghPluginsRepo) = $this->getGitHubPluginsInfo($ghSetupPlugins);

		$gitHubPlugins = array_keys($ghPlugins); // Get just the names of the plugins.

		$pluginsToAdd = array_diff($gitHubPlugins, $installedPlugins);

		foreach ($pluginsToAdd as $pluginToAdd) {
			if (isset(array_flip($gitHubPlugins)[$pluginToAdd])) {
				$repoName = $ghPluginsRepo[$pluginToAdd];
				$version = $ghPlugins[$pluginToAdd];

				$this->installGHPlugin($repoName, $version);
			}

			WP_CLI::success("Plugin {$pluginToAdd} installed");
		}

		return;
	}

	private function installPaidPlugins(array $setup)
	{
		$paidSetupPlugins = $setup['plugins']['paid'] ?? [];

		if (empty($paidSetupPlugins)) {
			WP_CLI::log('There are no paid plugins to install.');
			return;
		}

		// We only need a list of plugin names, so we'll filter the above return value.
		$installedPlugins = array_map(function($pluginElement){
			return $pluginElement['name'];
		}, $this->getCurrentlyInstalledPlugins());

		$paidPlugins = array_keys($paidSetupPlugins); // Get just the names of the plugins.

		$pluginsToAdd = array_diff($paidPlugins, $installedPlugins);

		foreach ($pluginsToAdd as $pluginToAdd) {
			if (isset(array_flip($paidPlugins)[$pluginToAdd])) {
				$version = $paidSetupPlugins[$pluginToAdd];

				$this->installPaidPlugin($pluginToAdd, $version);
			}

			WP_CLI::success("Plugin {$pluginToAdd} installed");
		}

		return;
	}

	private function manageAllPlugins(array $setup)
	{

		// Delete unused plugins.
		$this->deletePlugins($setup);

		// Install all the other plugins.
		$this->installWpOrgPlugins($setup);
		$this->installGitHubPlugins($setup);
		$this->installPaidPlugins($setup);

		// Check plugin versions and update if needed.
		$this->updatePlugins($setup);
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

	private function getSetupFile(): array
	{
		$setupFile = $this->getProjectConfigRootPath() . '/setup.json';

		if (\getenv('ES_TEST') === '1') {
			$setupFile = dirname(__FILE__) . '/setup.json';
		}

		if (!\file_exists($setupFile)) {
			throw FileMissing::missingFileOnPath($setupFile);
		}

		return (array)json_decode((string)\file_get_contents($setupFile), true);
	}

	/**
	 * @param string $fields Comma separated list of fields to return. Default is name.
	 *
	 * @return mixed
	 */
	private function getCurrentlyInstalledPlugins(string $fields = 'name')
	{
		// Get installed plugins.
		$installedPluginsList = WP_CLI::runcommand("plugin list --fields={$fields} --format=json", $this->cliOptions);

		return json_decode($installedPluginsList, true);
	}

	/**
	 * Return the information about the GH plugins
	 *
	 * @param array $ghSetupPlugins List of plugins located on GitHub.
	 *
	 * @return array[]
	 */
	private function getGitHubPluginsInfo(array $ghSetupPlugins): array
	{
		$ghPlugins = [];
		$ghPluginsRepo = [];

		if (!empty($ghSetupPlugins)) {
			/**
			 * We need to replace the package notifier from GH,
			 * because wp plugin list returns just the plugin name.
			 * But we also need to have some pointer about the package.
			 */
			foreach ($ghSetupPlugins as $ghPluginName => $ghPluginVersion) {
				$cleanedUpName = str_replace('/', '', strstr($ghPluginName, '/'));

				$ghPlugins[$cleanedUpName] = $ghPluginVersion;
				$ghPluginsRepo[$cleanedUpName] = $ghPluginName;
			}
		}

		return [$ghPlugins, $ghPluginsRepo];
	}

	/**
	 * Delete plugins that are located locally, but not in the setup.json
	 *
	 * @return void
	 */
	private function deletePlugins(array $setup)
	{
		$coreSetupPlugins = $setup['plugins']['core'] ?? [];
		$ghSetupPlugins = $setup['plugins']['github'] ?? [];
		$paidSetupPlugins = $setup['plugins']['paid'] ?? [];
		$folderSetupPlugins = $setup['plugins']['folder'] ?? [];

		// We only need a list of plugin names, so we'll filter the above return value.
		$installedPlugins = array_map(function($pluginElement){
			return $pluginElement['name'];
		}, $this->getCurrentlyInstalledPlugins());

		list($ghPlugins) = $this->getGitHubPluginsInfo($ghSetupPlugins);

		$gitHubPlugins = array_keys($ghPlugins); // Get just the names of the plugins.
		$paidPlugins = array_keys($paidSetupPlugins); // Get just the names of the plugins.
		$folderPlugins = array_keys($folderSetupPlugins); // Get just the names of the plugins.

		$setupPluginList = array_merge(
			array_keys($coreSetupPlugins),
			$gitHubPlugins,
			$paidPlugins,
			$folderPlugins
		);

		$pluginsToRemove = array_diff($installedPlugins, $setupPluginList);

		WP_CLI::log('Remove plugins');

		foreach ($pluginsToRemove as $pluginToRemove) {
			WP_CLI::runcommand("plugin delete {$pluginToRemove}", $this->cliOptions);
			WP_CLI::log("Plugin {$pluginToRemove} removed");
		}
	}

	private function updatePlugins(array $setup)
	{
		$currentlyInstalledPlugins = $this->getCurrentlyInstalledPlugins('name,version');

		$currentVersions = [];

		foreach ($currentlyInstalledPlugins as $pluginDetails) {
			$currentVersions[$pluginDetails['name']] = $pluginDetails['version'];
		}

		$coreSetupPlugins = $setup['plugins']['core'] ?? [];
		$ghSetupPlugins = $setup['plugins']['github'] ?? [];
		$paidSetupPlugins = $setup['plugins']['paid'] ?? [];
		$folderSetupPlugins = $setup['plugins']['folder'] ?? [];

		list($ghPlugins, $ghPluginsRepo) = $this->getGitHubPluginsInfo($ghSetupPlugins);

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

			WP_CLI::success("Plugin {$pluginName} updated");
		}
	}
}
