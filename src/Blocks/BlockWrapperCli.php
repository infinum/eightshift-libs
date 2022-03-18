<?php

/**
 * Class that registers WPCLI command for Blocks Wrapper.
 *
 * @package EightshiftLibs\Blocks
 */

declare(strict_types=1);

namespace EightshiftLibs\Blocks;

use EightshiftLibs\Cli\AbstractCli;
use WP_CLI;

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
	public const OUTPUT_DIR = 'src' . \DIRECTORY_SEPARATOR . 'Blocks' . \DIRECTORY_SEPARATOR . 'wrapper';

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
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Copy Wrapper from library to your project.',
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		// Get Props.
		$name = 'wrapper';

		// Set optional arguments.
		$skipExisting = $this->getSkipExisting($assocArgs);

		$root = $this->getProjectRootPath();
		$rootNode = $this->getFrontendLibsBlockPath();

		$ds = \DIRECTORY_SEPARATOR;

		$path = static::OUTPUT_DIR;
		$sourcePathFolder = $rootNode . $ds . static::OUTPUT_DIR . $ds;
		$sourcePath = "{$sourcePathFolder}";

		if (!\getenv('TEST')) {
			$destinationPath = $root . $ds . $path;
		} else {
			$destinationPath = $this->getProjectRootPath(true) . '/cliOutput';
		}

		// Destination exists.
		if (\file_exists($destinationPath) && $skipExisting === false) {
			self::cliError(
				\sprintf( // phpcs:ignore Eightshift.Commenting.FunctionComment.WrongStyle
					'The wrapper exists in your project on this "%s" path. Please check or remove that folder before running this command again.',
					$destinationPath
				)
			);
		} else {
			\mkdir("{$destinationPath}/");
		}

		$this->copyRecursively($sourcePath, "{$destinationPath}/");

		WP_CLI::success('Wrapper successfully moved to your project.');

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
