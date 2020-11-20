<?php

/**
 * Class that registers WPCLI command for Blocks Block.
 *
 * @package EightshiftLibs\Blocks
 */

declare(strict_types=1);

namespace EightshiftLibs\Blocks;

use EightshiftLibs\Cli\AbstractCli;
use WP_CLI\ExitException;

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
	public const OUTPUT_DIR = 'src/Blocks/custom';

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
	 * Get WPCLI command doc
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
		$destinationPath = $root . '/' . $path;

		// Source doesn't exist.
		if (!file_exists($sourcePath)) {
			$nameList = '';
			foreach (array_diff(scandir($sourcePathFolder), ['..', '.']) as $item) {
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

			try {
				\WP_CLI::error(
					"The block '{$sourcePath}' doesn\'t exist in our library."
				);
			} catch (ExitException $e) {
				exit("{$e->getCode()}: {$e->getMessage()}");
			}
		}

		// Destination exists.
		if (file_exists($destinationPath) && $skipExisting === false) {
			try {
				\WP_CLI::error(
					sprintf(
						'The block in you project exists on this "%s" path. Please check or remove that folder before running this command again.',
						$destinationPath
					)
				);
			} catch (ExitException $e) {
				exit("{$e->getCode()}: {$e->getMessage()}");
			}
		} else {
			system("mkdir -p {$destinationPath}/");
		}

		system("cp -R {$sourcePath}/. {$destinationPath}/");

		\WP_CLI::success('Block successfully moved to your project.');

		\WP_CLI::log('--------------------------------------------------');

		foreach ($this->getFullBlocksFiles($name) as $file) {
			// Set output file path.
			try {
				$class = $this->getExampleTemplate($destinationPath, $file, true);
			} catch (ExitException $e) {
				exit("{$e->getCode()}: {$e->getMessage()}");
			}

			if (!empty($class)) {
				$class = $this->renameProjectName($assocArgs, $class);

				try {
					$class = $this->renameNamespace($assocArgs, $class);
				} catch (ExitException $e) {
					exit("{$e->getCode()}: {$e->getMessage()}");
				}

				try {
					$class = $this->renameTextDomainFrontendLibs($assocArgs, $class);
				} catch (ExitException $e) {
					exit("{$e->getCode()}: {$e->getMessage()}");
				}

				try {
					$class = $this->renameUseFrontendLibs($assocArgs, $class);
				} catch (ExitException $e) {
					exit("{$e->getCode()}: {$e->getMessage()}");
				}

				// Output final class to new file/folder and finish.
				try {
					$this->outputWrite($path, $file, $class, ['skip_existing' => true]);
				} catch (ExitException $e) {
					exit("{$e->getCode()}: {$e->getMessage()}");
				}
			}
		}

		\WP_CLI::log('--------------------------------------------------');

		\WP_CLI::success('Please start `npm start` again to make sure everything works correctly.');
	}
}
