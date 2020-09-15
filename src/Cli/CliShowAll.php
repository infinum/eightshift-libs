<?php

/**
 * Class that registers WPCLI command for Development Show All.
 * Only used for development and can't be called via WPCLI.
 * It will output all commands at the same time but it will not run them!
 *
 * @package EightshiftLibs\Cli
 */

declare(strict_types=1);

namespace EightshiftLibs\Cli;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class CliShowAll
 */
class CliShowAll extends AbstractCli
{

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'show_all';
	}

	/**
	 * Shows all commands
	 *
	 * @param array $args Array of arguments form terminal.
	 * @param array $assocArgs Array of associative arguments form terminal.
	 *
	 * @throws \ReflectionException Exception in the case a class is missing.
	 */
	public function __invoke(array $args, array $assocArgs)
	{

		\WP_CLI::log(\WP_CLI::colorize('%mCommands for wp-cli and development:%n'));
		$this->getEvalLoop(Cli::CLASSES_LIST);
		\WP_CLI::log('-----------------------------------------');

		\WP_CLI::log(\WP_CLI::colorize('%mCommands for wp-cli only:%n'));
		$this->getEvalLoop(Cli::PUBLIC_CLASSES);
		\WP_CLI::log('-----------------------------------------');

		\WP_CLI::log(\WP_CLI::colorize('%mCommands for development:%n'));
		$this->getEvalLoop(Cli::DEVELOP_CLASSES);
		\WP_CLI::log('-----------------------------------------');

		\WP_CLI::log(\WP_CLI::colorize('%mCommands for project setup:%n'));
		$this->getEvalLoop(Cli::SETUP_CLASSES);
		\WP_CLI::log('-----------------------------------------');

		\WP_CLI::success('All commands are outputed.');
	}
}
