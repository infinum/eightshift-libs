<?php

/**
 * Class that registers WPCLI command for CiExcludeCli.
 *
 * @package EightshiftLibs\CiExclude
 */

declare(strict_types=1);

namespace EightshiftLibs\CiExclude;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class CiExcludeCli
 */
class CiExcludeCli extends AbstractCli
{

	/**
	 * Output dir relative path.
	 */
	public const OUTPUT_DIR = '../../../';

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'init_ci_exclude';
	}

	/**
	 * Define default develop props.
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
			'shortdesc' => 'Initialize Command for building your projects CI exclude file.',
			'synopsis' => [
				[
					'type'        => 'assoc',
					'name'        => 'root',
					'description' => 'Define project root relative to initialization file of WP CLI.',
					'optional'    => true,
				],
				[
					'type'        => 'assoc',
					'name'        => 'project_name',
					'description' => 'Set project file name, if theme use theme folder name, if plugin use plugin folder name.',
					'optional'    => true,
				],
				[
					'type'        => 'assoc',
					'name'        => 'project_type',
					'description' => 'Set project file name, if theme use theme folder name, if plugin use plugin folder name. Default is themes.',
					'optional'    => true,
				],
			],
		];
	}

	public function __invoke(array $args, array $assocArgs) // phpcs:ignore
	{

		// Get Props.
		$root = $assocArgs['root'] ?? static::OUTPUT_DIR;

		// Read the template contents, and replace the placeholders with provided variables.
		$class = $this->getExampleTemplate(__DIR__, 'ci-exclude.txt');

		// Replace stuff in file.
		$class = $this->renameProjectName($assocArgs, $class);
		$class = $this->renameProjectType($assocArgs, $class);

		// Output final class to new file/folder and finish.
		$this->outputWrite($root, 'ci-exclude.txt', $class);
	}
}
