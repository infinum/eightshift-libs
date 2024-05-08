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
		$assocArgs = $this->prepareArgs($assocArgs);
		$this->getIntroText($assocArgs);

		$assocArgs['actionOutput'] = 'file created';
		$assocArgs[self::ARG_IS_SETUP] = 'true';
		$assocArgs[self::ARG_SKIP_EXISTING] = 'true';

		$sep = \DIRECTORY_SEPARATOR;
		$dir = __DIR__ . "{$sep}theme";
		$files = \array_diff(\scandir($dir), ['..', '.']);

		$destionation = Helpers::getProjectPaths('themeRoot');

		$this->cleanUpInitialBoilerplate();

		foreach ($files as $file) {
			if ($file === '.' || $file === '..') {
				continue;
			}

			$this->getExampleTemplate($dir, $file)
				->renameGlobals($assocArgs)
				->outputWrite($destionation, $file, $assocArgs);
		}

		$this->initMandatoryAfter();
	}
}
