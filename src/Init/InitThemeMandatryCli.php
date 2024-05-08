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
		return [
			'path' => Helpers::getProjectPaths('projectRoot'),
			'file_name' => 'setup.json',
			'source_path' => __DIR__,
		];
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
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'path',
					'description' => 'Define absolute folder path where setup file will be created.',
					'optional' => true,
					'default' => $this->getDefaultArg('path'),
				],
				[
					'type' => 'assoc',
					'name' => 'file_name',
					'description' => 'Define file that will be created in the path location.',
					'optional' => true,
					'default' => $this->getDefaultArg('file_name'),
				],
			],
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

		// Get Props.
		$path = $this->getArg($assocArgs, 'path');
		$fileName = $this->getArg($assocArgs, 'file_name');
		$sourcePath = $this->getArg($assocArgs, 'source_path');

		$sep = \DIRECTORY_SEPARATOR;
		$files = \array_diff(\scandir("{$sep}files"), ['..', '.']);

		foreach ($files as $file) {
			if ($file === '.' || $file === '..') {
				continue;
			}

			$this->getExampleTemplate($sourcePath, $fileName)
			->outputWrite($path, $fileName, $assocArgs);
		}
	}
}
