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
	 * Get WPCLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'DEVELOP - Used to show all commands.',
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs) // phpcs:ignore
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

		\WP_CLI::success('All commands are outputted.');
	}
}
