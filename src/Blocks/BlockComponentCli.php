<?php

/**
 * Class that registers WPCLI command for Blocks Components.
 *
 * @package EightshiftLibs\Blocks
 */

declare(strict_types=1);

namespace EightshiftLibs\Blocks;

use EightshiftLibs\Cli\AbstractCli;
use WP_CLI\ExitException;

/**
 * Class BlockComponentCli
 */
class BlockComponentCli extends AbstractCli
{

	/**
	 * Output dir relative path
	 *
	 * @var string
	 */
	public const OUTPUT_DIR = 'src/Blocks/components';

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'use_component';
	}

	/**
	 * Get WPCLI command doc
	 *
	 * @return array
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Copy Component from library to your project.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'name',
					'description' => 'Specify component name.',
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
				"Please check the docs for all available components."
			);
			\WP_CLI::log(
				"You can find all available components on this link: https://infinum.github.io/eightshift-docs/storybook/."
			);
			\WP_CLI::log(
				"Or here is the list of all available component names: \n{$nameList}"
			);

			try {
				\WP_CLI::error(
					"The component '{$sourcePath}' doesn\'t exist in our library."
				);
			} catch (ExitException $e) {
				exit("{$e->getCode()}: {$e->getMessage()}");
			}
		}

		// Destination exists.
		if (file_exists($destinationPath) && $skipExisting === false) {
			try {
				\WP_CLI::error(
				/* translators: %s will be replaced with the path. */
					sprintf(
						'The component in you project exists on this "%s" path. Please check or remove that folder before running this command again.',
						$destinationPath
					)
				);
			} catch (ExitException $e) {
				exit("{$e->getCode()}: {$e->getMessage()}");
			}
		}

		system("cp -R {$sourcePath}/. {$destinationPath}/");

		\WP_CLI::success('Component successfully moved to your project.');

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
