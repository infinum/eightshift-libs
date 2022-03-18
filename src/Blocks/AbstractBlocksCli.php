<?php

/**
 * Class that hold abstractions for for Blocks CLI
 *
 * @package EightshiftLibs\Blocks
 */

declare(strict_types=1);

namespace EightshiftLibs\Blocks;

use EightshiftLibs\Cli\AbstractCli;
use FilesystemIterator;

/**
 * Abstract class used for Blocks and Components
 */
abstract class AbstractBlocksCli extends AbstractCli
{
	/**
	 * Move block/component to project folder.
	 *
	 * @param array<string, mixed> $assocArgs Array of arguments from WP-CLI command.
	 * @param string $outputDir Output dir path.
	 * @param bool $isComponents Is output used for components.
	 *
	 * @return void
	 */
	protected function blocksMove(array $assocArgs, string $outputDir, bool $isComponents = false): void
	{
		// Get Props.
		$name = $assocArgs['name'] ?? '';

		// Set optional arguments.
		$skipExisting = $this->getSkipExisting($assocArgs);

		$root = $this->getProjectRootPath();
		$rootNode = $this->getFrontendLibsBlockPath();

		$sourcePathFolder = "{$rootNode}/{$outputDir}/";

		$blocks = scandir($sourcePathFolder);
		$blocksFullList = array_diff((array)$blocks, ['..', '.']);

		$blocks = [$name];

		// If you pass a name "all" it will move all blocks/components to the project.
		if ($name === 'all') {
			$skipExisting = true;
			$blocks = $blocksFullList;
		}

		// Iterate blocks/components.
		foreach ($blocks as $block) {
			$path = "{$outputDir}/{$block}";
			$sourcePath = "{$sourcePathFolder}{$block}";

			if (!getenv('TEST')) {
				$destinationPath = $root . DIRECTORY_SEPARATOR . $path;
			} else {
				$destinationPath = $this->getProjectRootPath(true) . '/cliOutput';
			}

			$typePlural = !$isComponents ?  'blocks' : 'components';
			$typeSingular = !$isComponents ?  'block' : 'component';

			// Source doesn't exist.
			if (!file_exists($sourcePath)) {
				// Make a list for output.
				$blocksList = implode(PHP_EOL, $blocksFullList);

				\WP_CLI::log(
					"Please check the docs for all available {$typePlural}."
				);
				\WP_CLI::log(
					"You can find all available {$typePlural} on this link: https://infinum.github.io/eightshift-docs/storybook/."
				);
				\WP_CLI::log(
					"Or here is the list of all available {$typeSingular} names: \n{$blocksList}"
				);

				self::cliError("The {$typeSingular} '{$sourcePath}' doesn\'t exist in our library.");
			}

			// Destination exists.
			if (file_exists($destinationPath) && $skipExisting === false) {
				self::cliError(
					sprintf(
						'The %s in you project exists on this "%s" path. Please check or remove that folder before running this command again.',
						$typeSingular,
						$destinationPath,
					)
				);
			}

			// Move all files from library to project.
			$this->moveBlock($destinationPath, $sourcePath, $block, $assocArgs, $path, $typeSingular);
		}

		\WP_CLI::success('Please start `npm start` again to make sure everything works correctly.');
	}

	/**
	 * Move block/component from frontend libs to project.
	 *
	 * @param string $destinationPath Path where to move.
	 * @param string $sourcePath Path of the block/component.
	 * @param string $name Name of block/component.
	 * @param array<string, mixed> $assocArgs WP-CLI command arguments.
	 * @param string $path Path to write.
	 * @param string $typeSingular If block or component output string.
	 *
	 * @return void
	 */
	private function moveBlock(string $destinationPath, string $sourcePath, string $name, array $assocArgs, string $path, string $typeSingular): void
	{
		// Create folder in project if missing.
		mkdir("{$destinationPath}/");

		// Move block/component to project folder.
		$this->copyRecursively($sourcePath, "{$destinationPath}/");

		$typeSingular = ucfirst($typeSingular);

		\WP_CLI::success("{$typeSingular} successfully moved to your project.");

		\WP_CLI::log('--------------------------------------------------');

		// Move all files from library to project.
		foreach ($this->getFullBlocksFiles($name) as $file) {
			// Set output file path.
			$class = $this->getExampleTemplate($destinationPath, $file, true);

			if (!empty($class->fileContents)) {
				$class->renameProjectName($assocArgs)
					->renameNamespace($assocArgs)
					->renameTextDomainFrontendLibs($assocArgs)
					->renameUseFrontendLibs($assocArgs)
					->outputWrite($path, $file, ['skip_existing' => true]);
			}
		}

		\WP_CLI::log('--------------------------------------------------');
	}
}
