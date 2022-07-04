<?php

/**
 * Class that registers WPCLI command for CiExcludeCli.
 *
 * @package EightshiftLibs\CiExclude
 */

declare(strict_types=1);

namespace EightshiftLibs\CiExclude;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliCreate;
use EightshiftLibs\Helpers\Components;

/**
 * Class CiExcludeCli
 */
class CiExcludeCli extends AbstractCli
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
		return 'ci_exclude';
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
			'project_name' => 'eightshift-boilerplate',
			'project_type' => 'themes',
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
			'shortdesc' => 'Copy text file for building your projects continuous integration exclude file.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'path',
					'description' => 'Define absolute folder path where exclude file file will be created.',
					'optional' => true,
					'default' => $this->getDefaultArg('path'),
				],
				[
					'type' => 'assoc',
					'name' => 'project_name',
					'description' => 'Set project file name, if theme use theme folder name, if plugin use plugin folder name.',
					'optional' => true,
					'default' => $this->getDefaultArg('project_name'),
				],
				[
					'type' => 'assoc',
					'name' => 'project_type',
					'description' => 'Set project file name, if theme use theme folder name, if plugin use plugin folder name. Default is themes.',
					'optional' => true,
					'default' => $this->getDefaultArg('project_type'),
				],
			],
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Used to list all files you want to exclude in your continuous integration process. Generally used in GitHub Actions or any other tool for continuous integration.
				This file will be copied to your project root folder.

				## EXAMPLES

				# Copy file:
				$ wp boilerplate {$this->getCommandParentName()} {$this->getCommandName()}

				## RESOURCES

				File will be created from this example:
				https://github.com/infinum/eightshift-libs/blob/develop/src/Build/BuildExample.php
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		// Get Props.
		$path = $this->getArg($assocArgs, 'path');
		$projectName = $this->getArg($assocArgs, 'project_name');
		$projectType = $this->getArg($assocArgs, 'project_type');

		// Read the template contents, and replace the placeholders with provided variables.
		$this->getExampleTemplate(__DIR__, $this->getClassShortName())
			->searchReplaceString('<?php $output = \'', '')
			->searchReplaceString('\';', '')
			->searchReplaceString($this->getArgTemplate('project_name'), $projectName)
			->searchReplaceString($this->getArgTemplate('project_type'), $projectType)
			->outputWrite($path, 'ci-exclude.txt', $assocArgs);
	}
}
