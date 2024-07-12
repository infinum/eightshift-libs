<?php

/**
 * Class that registers WP-CLI command initial setup of theme project using npx command.
 *
 * @package EightshiftLibs\InitSetup
 */

declare(strict_types=1);

namespace EightshiftLibs\InitSetup;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliInitSetup;
use EightshiftLibs\Helpers\Helpers;
use WP_CLI;

/**
 * Class InitThemeSetupCli
 */
class InitThemeSetupCli extends AbstractCli
{
	/**
	 * Get WPCLI command parent name
	 *
	 * @return string
	 */
	public function getCommandParentName(): string
	{
		return CliInitSetup::COMMAND_NAME;
	}

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'theme';
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
			'shortdesc' => 'Setup theme project with initial boilerplate used with npx command. This command should never be run manually.',
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
				[
					'type' => 'assoc',
					'name' => self::ARG_FRONTEND_LIBS_VERSION,
					'description' => 'Define Eightshift frontend libs version.',
					'optional' => true,
				],
				[
					'type' => 'assoc',
					'name' => self::ARG_FRONTEND_LIBS_TYPE,
					'description' => 'Define Eightshift frontend libs type.',
					'optional' => true,
				],
			],
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Used to setup theme from the initial boilerplate.
				This command should never be run manually as it will break your project.

				## EXAMPLES

				# Setup theme project with initial boilerplate:
				$ wp {$this->commandParentName} {$this->getCommandParentName()} {$this->getCommandName()}
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		$assocArgs = $this->prepareSetupArgs($assocArgs);

		$textdomain = $assocArgs[self::ARG_TEXTDOMAIN];
		$frontendLibsType = $assocArgs[self::ARG_FRONTEND_LIBS_TYPE];

		$this->getIntroText($assocArgs);

		$sep = \DIRECTORY_SEPARATOR;
		$dir = __DIR__ . "{$sep}{$frontendLibsType}{$sep}theme";
		$files = \array_diff(\scandir($dir), ['..', '.']);

		$destionation = Helpers::getProjectPaths('themeRoot');

		$this->cliLog('--------------------------------------------------', 'C');
		$this->cliLog("Moving theme mandatory files", 'C');

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
		$this->cliLog("Changing the setup theme to the new theme with name {$textdomain}", 'C');
		\rename($destionation, $newDestionation); // phpcs:ignore WordPress.WP.AlternativeFunctions.rename_rename

		$this->initMandatoryBackendAfter(
			$assocArgs[self::ARG_LIBS_VERSION],
			$newDestionation
		);

		$this->initMandatoryFrontendAfter(
			$assocArgs[self::ARG_FRONTEND_LIBS_VERSION],
			$assocArgs[self::ARG_FRONTEND_LIBS_TYPE],
			$newDestionation
		);

		$this->cleanUpInitialBoilerplate($newDestionation);

		$this->cliLog('--------------------------------------------------', 'C');
		$this->cliLog("Activating new theme", 'C');
		WP_CLI::runcommand("theme activate {$textdomain}");
		$this->cliLog('--------------------------------------------------', 'C');
		$this->cliLog("Installing theme service classes and blocks", 'C');
		WP_CLI::runcommand(\sprintf("boilerplate init theme --%s=true", self::ARG_GROUP_OUTPUT));
		$this->cliLog('--------------------------------------------------', 'C');
		$this->cliLog("Building the new theme assets", 'C');
		\shell_exec("cd {$newDestionation} && npm run build"); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.system_calls_shell_exec
		$this->cliLog('--------------------------------------------------', 'C');
		$this->cliLog("Finished", 'C');
		$this->cliLogAlert(
			"All the files have been created and you can start working on your awesome theme!
			Make sure to change directory in your terminal to the new theme directory by running:\n\n
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
