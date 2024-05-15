<?php

/**
 * Class that registers WP-CLI command initial setup of plugin project using npx command.
 *
 * @package EightshiftLibs\Init
 */

declare(strict_types=1);

namespace EightshiftLibs\Init;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliInit;
use EightshiftLibs\Helpers\Helpers;
use WP_CLI;

/**
 * Class InitPluginSetupCli
 */
class InitPluginSetupCli extends AbstractCli
{
	/**
	 * Get WPCLI command parent name
	 *
	 * @return string
	 */
	public function getCommandParentName(): string
	{
		return CliInit::COMMAND_NAME;
	}

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'plugin-setup';
	}

	/**
	 * Define default arguments.
	 *
	 * @return array<string, int|string|boolean>
	 */
	public function getDefaultArgs(): array
	{
		return [];
	}

	/**
	 * Get WPCLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Setup plugin project with initial boilerplate used with npx command. This command should never be run manually.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => self::ARG_PROJECT_NAME,
					'description' => 'Define your projects name.',
					'optional' => false,
				],
				[
					'type' => 'assoc',
					'name' => self::ARG_PROJECT_DESCRIPTION,
					'description' => 'Define your projects description.',
					'optional' => true,
				],
				[
					'type' => 'assoc',
					'name' => self::ARG_PROJECT_AUTHOR,
					'description' => 'Define your projects author.',
					'optional' => true,
				],
				[
					'type' => 'assoc',
					'name' => self::ARG_PROJECT_AUTHOR_URL,
					'description' => 'Define your projects author url.',
					'optional' => true,
				],
				[
					'type' => 'assoc',
					'name' => self::ARG_PROJECT_VERSION,
					'description' => 'Define your projects version.',
					'optional' => true,
				],
				[
					'type' => 'assoc',
					'name' => self::ARG_SITE_URL,
					'description' => 'Define your projects url for webpack build.',
					'optional' => true,
				],
				[
					'type' => 'assoc',
					'name' => self::ARG_LIBS_VERSION,
					'description' => 'Define Eightshift libs version.',
					'optional' => true,
				],
			],
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Used to setup plugin from the initial boilerplate.
				This command should never be run manually as it will break your project.

				## EXAMPLES

				# Setup plugin project with initial boilerplate:
				$ wp {$this->commandParentName} {$this->getCommandParentName()} {$this->getCommandName()}
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		$assocArgs = $this->prepareSetupArgs($assocArgs);

		$textdomain = $assocArgs[self::ARG_TEXTDOMAIN];

		$this->getIntroText($assocArgs);

		$sep = \DIRECTORY_SEPARATOR;
		$dir = __DIR__ . "{$sep}plugin";
		$files = \array_diff(\scandir($dir), ['..', '.']);

		$destionation = Helpers::getProjectPaths('pluginRoot');

		$this->cliLog('--------------------------------------------------', 'C');
		$this->cliLog("Moving plugin mandatory files", 'C');

		foreach ($files as $file) {
			if ($file === '.' || $file === '..') {
				continue;
			}

			$this->getExampleTemplate($dir, $file)
				->renameGlobals($assocArgs)
				->outputWrite($destionation, $file, $assocArgs);
		}

		$newDestionation = Helpers::joinPaths([\dirname($destionation), $textdomain]);

		$this->cliLog('--------------------------------------------------', 'C');
		$this->cliLog("Changing the setup plugin to the new plugin with name {$textdomain}", 'C');
		\rename($destionation, $newDestionation); // phpcs:ignore WordPress.WP.AlternativeFunctions.rename_rename

		$this->initMandatoryAfter($assocArgs[self::ARG_LIBS_VERSION], $newDestionation);
		$this->cleanUpInitialBoilerplate($newDestionation);

		$this->cliLog('--------------------------------------------------', 'C');
		$this->cliLog("Activating new plugin", 'C');
		WP_CLI::runcommand("plugin activate {$textdomain}");
		$this->cliLog('--------------------------------------------------', 'C');
		$this->cliLog("Installing plugin service classes and blocks", 'C');
		WP_CLI::runcommand(\sprintf("boilerplate init plugin --%s=true", self::ARG_GROUP_OUTPUT));
		$this->cliLog('--------------------------------------------------', 'C');
		$this->cliLog("Building the new plugin assets", 'C');
		\shell_exec("cd {$newDestionation} && npm run build");
		$this->cliLog('--------------------------------------------------', 'C');
		$this->cliLog("Finished", 'C');
		$this->cliLogAlert(
			"All the files have been created and you can start working on your awesome plugin!
			Make sure you move to your terminal to new plugin by running:\n
			cd {$newDestionation}",
			'success',
			'Almost there!'
		);
		$this->cliLogAlert(
			"To start the development run:\n
			npm run start",
			'success',
		);
		$this->cliLogAlert(
			"To build the production run:\n
			npm run build",
			'success',
		);
	}
}
