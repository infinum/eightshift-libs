<?php

/**
 * Class that registers WPCLI command for Blocks Block.
 *
 * @package EightshiftLibs\Blocks
 */

declare(strict_types=1);

namespace EightshiftLibs\Blocks;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class BlockCli
 */
class BlockCli extends AbstractCli
{

	/**
	 * Output dir relative path
	 *
	 * @var string
	 */
	public const OUTPUT_DIR = 'src' . DIRECTORY_SEPARATOR . 'Blocks' . DIRECTORY_SEPARATOR . 'custom';

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'use_block';
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
			'name' => $args[1] ?? 'button',
		];
	}

	/**
	 * Get WPCLI command docblock
	 *
	 * @return array
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Copy Block from library to your project.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'name',
					'description' => 'Specify block name.',
					'optional' => false,
				],
			],
		];
	}

	public function __invoke(array $args, array $assocArgs) // phpcs:ignore
	{
		// Get Props.
		$name = $assocArgs['name'] ?? '';

		// Set optional arguments.
		$skipExisting = $this->getSkipExisting($assocArgs);

		$root = $this->getProjectRootPath();
		$rootNode = $this->getFrontendLibsBlockPath();

		$path = static::OUTPUT_DIR . '/' . $name;
		$sourcePathFolder = $rootNode . '/' . static::OUTPUT_DIR . '/';
		$sourcePath = "{$sourcePathFolder}{$name}";

		if (!getenv('TEST')) {
			$destinationPath = $root . '/' . $path;
		} else {
			$destinationPath = $this->getProjectRootPath(true) . '/cliOutput';
		}

		// Source doesn't exist.
		if (!file_exists($sourcePath)) {
			$nameList = '';
			$filesList = scandir($sourcePathFolder);

			if (!$filesList) {
				self::cliError("The folder in the '{$sourcePath}' seems to be empty.");
			}

			foreach (array_diff((array)$filesList, ['..', '.']) as $item) {
				$nameList .= "- {$item} \n";
			}

			\WP_CLI::log(
				"Please check the docs for all available blocks."
			);
			\WP_CLI::log(
				"You can find all available blocks on this link: https://infinum.github.io/eightshift-docs/storybook/."
			);
			\WP_CLI::log(
				"Or here is the list of all available block names: \n{$nameList}"
			);

			self::cliError("The block '{$sourcePath}' doesn\'t exist in our library.");
		}

		// Destination exists.
		if (file_exists($destinationPath) && $skipExisting === false) {
			self::cliError(
				sprintf(
					'The block in you project exists on this "%s" path. Please check or remove that folder before running this command again.',
					$destinationPath
				)
			);
		} else {
			system("mkdir -p {$destinationPath}/");
		}

		system("cp -R {$sourcePath}/. {$destinationPath}/");

		\WP_CLI::success('Block successfully moved to your project.');

		\WP_CLI::log('--------------------------------------------------');

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

		\WP_CLI::success('Please start `npm start` again to make sure everything works correctly.');
	}
}
