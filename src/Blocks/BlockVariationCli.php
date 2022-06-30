<?php

/**
 * Class that registers WPCLI command for Blocks Variations.
 *
 * @package EightshiftLibs\Blocks
 */

declare(strict_types=1);

namespace EightshiftLibs\Blocks;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliBlocks;
use EightshiftLibs\Helpers\Components;
use WP_CLI;

/**
 * Class BlockVariationCli
 */
class BlockVariationCli extends AbstractCli
{
	/**
	 * Output dir relative path
	 *
	 * @var string
	 */
	public const OUTPUT_DIR = 'src' . \DIRECTORY_SEPARATOR . 'Blocks' . \DIRECTORY_SEPARATOR . 'variations';

	/**
	 * Get WPCLI command parent name
	 *
	 * @return string
	 */
	public function getCommandParentName(): string
	{
		return CliBlocks::COMMAND_NAME;
	}

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'variation';
	}

	/**
	 * Define default arguments.
	 *
	 * @return array<string, int|string|boolean>
	 */
	public function getDefaultArgs(): array
	{
		return [
			'name' => 'button-block',
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
			'shortdesc' => 'Copy variation from our library to your project.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'name',
					'description' => 'Specify variation name.',
					'optional' => false,
				],
			],
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Used to copy pre-created variation from our library to your project. After copying you can modify the variation in any way you see fit.

				## EXAMPLES

				# Copy variation by name.
				$ wp boilerplate {$this->getCommandParentName()} {$this->getCommandName()} --name='button-block'

				## RESOURCES

				All our variations can be found here:
				https://github.com/infinum/eightshift-frontend-libs/tree/develop/blocks/init/src/Blocks/variations
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		// Get Props.
		$name = $this->getArg($assocArgs, 'name');

		// Set optional arguments.
		$skipExisting = $this->getSkipExisting($assocArgs);

		$root = Components::getProjectPaths('root');
		$rootNode = Components::getProjectPaths('blocksSource');
		$ds = \DIRECTORY_SEPARATOR;

		$path = static::OUTPUT_DIR . $ds . $name;
		$sourcePathFolder = $rootNode . $ds . static::OUTPUT_DIR . $ds;
		$sourcePath = "{$sourcePathFolder}{$name}";

		$destinationPath = "{$root}{$ds}{$path}";

		// Source doesn't exist.
		if (!\file_exists($sourcePath)) {
			$nameList = '';

			if (!\is_dir($sourcePathFolder)) {
				self::cliError("The variation source folder is missing!");
			}

			$filesList = \array_diff(\scandir($sourcePathFolder), ['..', '.']);

			foreach ($filesList as $item) {
				$nameList .= "- {$item} \n";
			}

			WP_CLI::log(
				"Please check the docs for all available variations."
			);
			WP_CLI::log(
				"You can find all available variations on this link: https://infinum.github.io/eightshift-docs/storybook/."
			);
			WP_CLI::log(
				"Or here is the list of all available variation names: \n{$nameList}"
			);

			self::cliError("The variation '{$sourcePath}' doesn\'t exist in our library.");
		}

		// Destination exists.
		if (\file_exists($destinationPath) && $skipExisting === false) {
			self::cliError(
				/* translators: %s will be replaced with the path. */
				\sprintf(
					'The variation in you project exists on this "%s" path. Please check or remove that folder before running this command again.',
					$destinationPath
				)
			);
		}

		// Move block/component to project folder.
		$this->copyRecursively($sourcePath, $destinationPath);

		WP_CLI::success('Variation successfully moved to your project.');

		WP_CLI::log('--------------------------------------------------');

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

		WP_CLI::log('--------------------------------------------------');

		WP_CLI::success('Please start `npm start` again to make sure everything works correctly.');
	}
}
