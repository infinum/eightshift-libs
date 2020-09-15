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

use EightshiftLibs\Cli\AbstractCli;

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
	 * Resets and runs all the commands in the classes list
	 *
	 * @param array $args      Array of arguments form terminal.
	 * @param array $assocArgs Array of associative arguments form terminal.
	 */
	public function __invoke(array $args, array $assocArgs)
	{

		$this->runReset();

		\WP_CLI::log('--------------------------------------------------');
		$this->getEvalLoop(Cli::CLASSES_LIST, true);
		\WP_CLI::log('--------------------------------------------------');

		\WP_CLI::success('All commands are finished.');
	}
}
