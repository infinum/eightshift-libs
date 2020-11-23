<?php

/**
 * Class that registers WPCLI command for Blocks Wrapper.
 *
 * @package EightshiftLibs\Blocks
 */

declare(strict_types=1);

namespace EightshiftLibs\Blocks;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\CliHelpers;

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
			self::cliError(
				/* translators: %s will be replaced with the path. */
				sprintf(
					'The wrapper exists in your project on this "%s" path. Please check or remove that folder before running this command again.',
					$destinationPath
				)
			);
		} else {
			system("mkdir -p {$destinationPath}/");
		}

		system("cp -R {$sourcePath}/. {$destinationPath}/");

		\WP_CLI::success('Wrapper successfully moved to your project.');

		\WP_CLI::log('--------------------------------------------------');

		foreach ($this->getFullBlocksFiles($name) as $file) {
			// Set output file path.
			$class = $this->getExampleTemplate($destinationPath, $file, true);

			if (!empty($class)) {
				$class = $this->renameProjectName($assocArgs, $class);

				$class = $this->renameNamespace($assocArgs, $class);

				$class = $this->renameTextDomainFrontendLibs($assocArgs, $class);

				$class = $this->renameUseFrontendLibs($assocArgs, $class);

				// Output final class to new file/folder and finish.
				$this->outputWrite($path, $file, $class, ['skip_existing' => true]);
			}
		}

		\WP_CLI::log('--------------------------------------------------');

		\WP_CLI::success('Please start `npm start` again to make sure everything works correctly.');
	}
}
