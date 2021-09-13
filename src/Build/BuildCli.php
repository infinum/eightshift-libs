<?php

/**
 * Class that registers WPCLI command for BuildCli.
 *
 * @package EightshiftLibs\Build
 */

declare(strict_types=1);

namespace EightshiftLibs\Build;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class BuildCli
 */
class BuildCli extends AbstractCli
{
	/**
	 * Init build command name.
	 *
	 * @var string
	 */
	public const COMMAND_NAME = 'init_build';

	/**
	 * Output dir relative path
	 *
	 * @var string
	 */
	public const OUTPUT_DIR = '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return self::COMMAND_NAME;
	}

	/**
	 * Define default develop props
	 *
	 * @param array $args WPCLI eval-file arguments.
	 *
	 * @return array
	 */
	public function getDevelopArgs(array $args): array
	{
		return [
			'root' => $args[1] ?? './',
		];
	}

	/**
	 * Get WPCLI command doc.
	 *
	 * @return array
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Initialize Command for building your project with one command, generally used on CI deployments.',
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
		];
	}

	public function __invoke(array $args, array $assocArgs) // phpcs:ignore
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
