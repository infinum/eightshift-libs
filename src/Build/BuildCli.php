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
	 * Output dir relative path
	 *
	 * @var string
	 */
	public const OUTPUT_DIR = '../../../';

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'init_build';
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
					'name' => 'skip_setup_file',
					'description' => 'If you already have setup.json file in the root of your project.',
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
		$skipSetupFile = $assocArgs['skip_setup_file'] ?? true;

		// Read the template contents, and replace the placeholders with provided variables.
		$class = $this->getExampleTemplate(__DIR__, $this->getClassShortName());

		// Replace stuff in file.
		$class = $this->renameProjectName($assocArgs, $class);
		$class = $this->renameProjectType($assocArgs, $class);
		$class = $this->renameTextDomain($assocArgs, $class);

		// Output final class to new file/folder and finish.
		$this->outputWrite($root . 'bin', $this->getClassShortName(), $class, $assocArgs);

		if (!$skipSetupFile) {
			// Get setup.json file.
			$json = $this->getExampleTemplate(dirname(__DIR__, 1), 'setup/setup.json');

			// Output json file to project root.
			$this->outputWrite($root, 'setup.json', $json, $assocArgs);
		}
	}
}
