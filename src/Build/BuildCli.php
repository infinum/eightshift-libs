<?php

/**
 * Class that registers WPCLI command for BuildCli.
 *
 * @package EightshiftLibs\Build
 */

declare(strict_types=1);

namespace EightshiftLibs\Build;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliCreate;
use EightshiftLibs\Helpers\Components;

/**
 * Class BuildCli
 */
class BuildCli extends AbstractCli
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
		return 'build';
	}

	/**
	 * Define default arguments.
	 *
	 * @return array<string, int|string|boolean>
	 */
	public function getDefaultArgs(): array
	{
		return [
			'path' => Components::getProjectPaths('projectRoot', 'bin'),
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
			'shortdesc' => 'Copy bash script for building your project with one command, generally used on CI deployments.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'path',
					'description' => 'Define absolute folder path where build script file will be created.',
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

				Used for building your project to production ready version in one command. Generally used in GitHub Actions or any other tool for continuous integration. This file will be copied to your project root under the bin folder.

				## EXAMPLES

				# Copy file:
				$ wp {$this->commandParentName} {$this->getCommandParentName()} {$this->getCommandName()}

				## RESOURCES

				File will be created from this example:
				https://github.com/infinum/eightshift-libs/blob/develop/src/Build/BuildExample.php
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		$this->getIntroText($assocArgs);

		// Get Props.
		$path = $this->getArg($assocArgs, 'path');

		// Read the template contents, and replace the placeholders with provided variables.
		$this->getExampleTemplate(__DIR__, $this->getClassShortName())
			->renameProjectName($assocArgs)
			->renameProjectType($assocArgs)
			->outputWrite($path, 'build.sh', $assocArgs);
	}
}
