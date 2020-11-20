<?php

/**
 * Class that registers WPCLI command for Blocks Wrapper.
 *
 * @package EightshiftLibs\Blocks
 */

declare(strict_types=1);

namespace EightshiftLibs\Blocks;

use EightshiftLibs\Cli\AbstractCli;
use WP_CLI\ExitException;

/**
 * Class BlockWrapperCli
 */
class BlockWrapperCli extends AbstractCli
{

	/**
	 * Output dir relative path
	 *
	 * @var string
	 */
	public const OUTPUT_DIR = 'src/Blocks/wrapper';

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'use_wrapper';
	}

	/**
	 * Get WPCLI command doc
	 *
	 * @return array
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Copy Wrapper from library to your project.',
		];
	}

	public function __invoke(array $args, array $assocArgs) // phpcs:ignore
	{
		// Get Props.
		$name = 'wrapper';

		// Set optional arguments.
		$skipExisting = $this->getSkipExisting($assocArgs);

		$root = $this->getProjectRootPath();
		$rootNode = $this->getFrontendLibsBlockPath();

		$path = static::OUTPUT_DIR;
		$sourcePathFolder = $rootNode . '/' . static::OUTPUT_DIR . '/';
		$sourcePath = "{$sourcePathFolder}";
		$destinationPath = $root . '/' . $path;

		// Destination exists.
		if (file_exists($destinationPath) && $skipExisting === false) {
			try {
				\WP_CLI::error(
				/* translators: %s will be replaced with the path. */
					sprintf(
						'The wrapper exists in your project on this "%s" path. Please check or remove that folder before running this command again.',
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

		\WP_CLI::success('Wrapper successfully moved to your project.');

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
