<?php

/**
 * Class that registers WPCLI command for Setup.
 *
 * @package EightshiftLibs\Setup
 */

declare(strict_types=1);

namespace EightshiftLibs\Setup;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliCreate;
use EightshiftLibs\Helpers\Components;

/**
 * Class SetupCli
 */
class SetupCli extends AbstractCli
{
	/**
	 * Get WPCLI command parent name
	 *
	 * @return string
	 */
	public function getCommandParentName(): string
	{
		return CliCreate::COMMAND_NAME;
	}

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'setup';
	}

	/**
	 * Define default arguments.
	 *
	 * @return array<string, int|string|boolean>
	 */
	public function getDefaultArgs(): array
	{
		return [
			'path' => Components::getProjectPaths('projectRoot'),
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
			'shortdesc' => 'Copy setup.json file used for automatic project setup and update.',
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

		// Get setup.json example file, and create the one in the project.
		$this->getExampleTemplate($sourcePath, $fileName)
			->outputWrite($path, $fileName, $assocArgs);
	}
}
