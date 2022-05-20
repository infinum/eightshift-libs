<?php

// phpcs:ignoreFile Generic.Files.LineLength.TooLong

/**
 * Class that registers WPCLI command for BuildCli.
 *
 * @package EightshiftLibs\Build
 */

declare(strict_types=1);

namespace EightshiftLibs\Build;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliProject;

/**
 * Class BuildCli
 */
class BuildCli extends AbstractCli
{
	/**
	 * Output dir relative path
	 *
	 * @var string
	 */
	public const OUTPUT_DIR = '..' . \DIRECTORY_SEPARATOR . '..' . \DIRECTORY_SEPARATOR . '..' . \DIRECTORY_SEPARATOR;

	/**
	 * Get WPCLI command parent name
	 *
	 * @return string
	 */
	public function getCommandParentName(): string
	{
		return CliProject::COMMAND_NAME;
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
	 * Define default develop props
	 *
	 * @param string[] $args WPCLI eval-file arguments.
	 *
	 * @return array<string, mixed>
	 */
	public function getDevelopArgs(array $args): array
	{
		return [
			'root' => $args[1] ?? './',
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
					'name' => 'root',
					'description' => 'Define project root relative to initialization file of WP CLI.',
					'optional' => true,
				],
				[
					'type' => 'assoc',
					'name' => 'project_name',
					'description' => 'Set project file name, if theme use theme folder name, if plugin use plugin folder name.',
					'optional' => true,
				],
				[
					'type' => 'assoc',
					'name' => 'project_type',
					'description' => 'Set project file name, if theme use theme folder name, if plugin use plugin folder name. Default is themes.',
					'optional' => true,
				],
			],
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Used for building you project to production ready version in one command. Generally used in GitHub Actions or any other tool for continuous integration. This file will be copied to your project root under the bin folder.

				## EXAMPLES

				# Create service class:
				$ wp boilerplate {$this->getCommandParentName()} {$this->getCommandName()}

				## RESOURCES

				Service class will be created from this example:
				https://github.com/infinum/eightshift-libs/blob/develop/src/Build/BuildExample.php
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		// Get Props.
		$root = $assocArgs['root'] ?? static::OUTPUT_DIR;

		// Read the template contents, and replace the placeholders with provided variables.
		$this->getExampleTemplate(__DIR__, $this->getClassShortName())
			->renameProjectName($assocArgs)
			->renameProjectType($assocArgs)
			->outputWrite($root . 'bin', 'build.sh', $assocArgs);
	}
}
