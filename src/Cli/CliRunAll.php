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

/**
 * Class CliRunAll
 */
class CliRunAll extends AbstractCli
{

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
	 * Get WPCLI command doc.
	 *
	 * @return array
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'DEVELOP - Used to run all commands.',
		];
	}

	public function __invoke(array $args, array $assocArgs) // phpcs:ignore
	{
		$this->runReset();

		\WP_CLI::log('--------------------------------------------------');

		$this->getEvalLoop(Cli::CLASSES_LIST, true);

		\WP_CLI::log('--------------------------------------------------');

		\WP_CLI::success('All commands are finished.');
	}
}
