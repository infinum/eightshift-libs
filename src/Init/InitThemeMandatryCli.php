<?php

/**
 * Class that registers WP-CLI command initial setup of theme project only mandatory files.
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
 * Class InitThemeMandatryCli
 */
class InitThemeMandatryCli extends AbstractCli
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
		return 'theme-mandatory';
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
			'shortdesc' => 'Copy all mandatory theme files to the project.',
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Used to list all your project configuration like themes and their versions,
				plugins (wp.org, paid, added to repo or from github release), core version, environments, etc.
				This file will be copied to your project root folder.

				## EXAMPLES

				# Copy file:
				$ wp {$this->commandParentName} {$this->getCommandParentName()} {$this->getCommandName()}

				## RESOURCES

				File will be created from this example:
				https://github.com/infinum/eightshift-libs/blob/develop/src/Setup/setup.json
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		$this->getIntroText($assocArgs);

		$assocArgs['actionOutput'] = 'created';

		$sep = \DIRECTORY_SEPARATOR;
		$dir = __DIR__ . "{$sep}theme";
		$files = \array_diff(\scandir($dir), ['..', '.']);

		$destionation = Helpers::getProjectPaths('themeRoot');

		foreach ($files as $file) {
			if ($file === '.' || $file === '..') {
				continue;
			}

			$this->getExampleTemplate($dir, $file)
				->renameNamespace($assocArgs)
				->renameUse($assocArgs)
				->outputWrite($destionation, $file, $assocArgs);
		}

		WP_CLI::runcommand("wp eval 'shell_exec(\"rm composer.lock\");'");
		WP_CLI::runcommand("wp eval 'shell_exec(\"composer install -n --no-cache\");'");
		WP_CLI::runcommand("wp eval 'shell_exec(\"npm install\");'");
	}
}
