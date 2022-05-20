<?php

/**
 * Class that registers WPCLI command for Development Run All.
 * Only used for development and can't be called via WPCLI.
 * It will run all commands at the same time.
 *
 * @package EightshiftLibs\Cli
 */

declare(strict_types=1);

namespace EightshiftLibs\Cli;

use WP_CLI;

/**
 * Class CliRunAll
 */
class CliRunAll extends AbstractCli
{
	/**
	 * Get WPCLI command parent name
	 *
	 * @return string
	 */
	public function getCommandParentName(): string
	{
		return 'develop';
	}

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'run_all';
	}

	/**
	 * Get WPCLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'DEVELOP - Used to run all commands.',
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		// Set output paths.
		$outputDir = $this->getOutputDir();

		// Create output dir if it doesn't exist.
		if (!\is_dir($outputDir)) {
			\mkdir($outputDir, 0755, true);
		}

		$this->runReset();

		WP_CLI::log('--------------------------------------------------');

		$this->getEvalLoop(Cli::CLASSES_LIST, true);

		WP_CLI::log('--------------------------------------------------');

		WP_CLI::success('All commands are finished.');
	}
}
