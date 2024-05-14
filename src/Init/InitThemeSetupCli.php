<?php

/**
 * Class that registers WP-CLI command initial setup of theme project using npx command.
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
		return CliInit::COMMAND_NAME;
	}

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'theme-setup';
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

		$groupOutput = $assocArgs[self::ARG_GROUP_OUTPUT];
		$gro

		$this->getIntroText();

		$sep = \DIRECTORY_SEPARATOR;
		$dir = __DIR__ . "{$sep}theme";
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

		$newDestionation = Helpers::joinPaths([\dirname($destionation), $assocArgs[self::ARG_TEXTDOMAIN]]);

		$this->cliLog('--------------------------------------------------', 'C');
		$this->cliLog("Changing the setup theme to the new theme name", 'C');
		\rename($destionation, $newDestionation); // phpcs:ignore WordPress.WP.AlternativeFunctions.rename_rename

		$this->initMandatoryAfter($assocArgs[self::ARG_LIBS_VERSION], $newDestionation);
		$this->cleanUpInitialBoilerplate($newDestionation);

		$this->cliLog('--------------------------------------------------', 'C');
		$this->cliLog("Activating new theme", 'C');
		WP_CLI::runcommand('theme activate ' . $assocArgs[self::ARG_TEXTDOMAIN]);
		$this->cliLog('--------------------------------------------------', 'C');
		$this->cliLog("Installing theme service classes", 'C');
		WP_CLI::runcommand(sprintf("boilerplate init theme --%s=true"), self::ARG_GROUP_OUTPUT);
		$this->cliLog('--------------------------------------------------', 'C');
		$this->cliLog("Building the new theme assets", 'C');
		WP_CLI::runcommand("eval 'shell_exec(\"cd {$newDestionation} && npm run build\");'");
		$this->cliLog('--------------------------------------------------', 'C');
		$this->cliLogAlert(
			"
			All the files have been copied and you can start working on your awesome theme!\n
			Make sure you move to your terminal to new theme by running:\n
			cd {$newDestionation}
			",
			'success',
			\__('Ready to go!', 'eightshift-libs')
		);
		$this->cliLogAlert(
			"To start the development run:\n
			npm run start
			",
			'success',
		);
		$this->cliLogAlert(
			"To build the production run:\n
			npm run buildrm
			",
			'success',
		);
	}
}
