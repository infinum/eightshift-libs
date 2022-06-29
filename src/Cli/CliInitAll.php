<?php

/**
 * Class that registers WPCLI command initial setup of theme project.
 *
 * @package EightshiftLibs\Cli
 */

declare(strict_types=1);

namespace EightshiftLibs\Cli;

use EightshiftLibs\Cli\ParentGroups\CliSetup;
use WP_CLI;

/**
 * Class CliInitAll
 */
class CliInitAll extends AbstractCli
{
	/**
	 * Get WPCLI command parent name
	 *
	 * @return string
	 */
	public function getCommandParentName(): string
	{
		return CliSetup::COMMAND_NAME;
	}

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'all';
	}

	/**
	 * Get WPCLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Initial setup for all service classes in the WordPress theme project.',
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Used to create ALL service classes in your project.
				This command is used only in develop mode. For this to work you must set global constant ES_DEVELOP_MODE to true.

				## EXAMPLES

				# Create all service classes:
				$ wp boilerplate {$this->getCommandParentName()} {$this->getCommandName()}
			"),

		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs) // phpcs:ignore
	{
		$classes = \array_merge(
			Cli::CLASSES_LIST,
			Cli::BLOCKS_CLASSES,
			Cli::SETUP_CLASSES
		);

		$this->getEvalLoop($classes, $assocArgs);

		WP_CLI::log('--------------------------------------------------');

		if (!\getenv('ES_TEST')) {
			WP_CLI::log((string)shell_exec('npm run start')); // phpcs:ignore
		}

		WP_CLI::log('--------------------------------------------------');

		WP_CLI::success('All commands are finished.');
	}
}
