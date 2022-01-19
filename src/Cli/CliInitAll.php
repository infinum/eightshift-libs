<?php

/**
 * Class that registers WPCLI command initial setup of theme project.
 *
 * @package EightshiftLibs\Cli
 */

declare(strict_types=1);

namespace EightshiftLibs\Cli;

use EightshiftLibs\Blocks\BlocksCli;
use EightshiftLibs\Config\ConfigCli;
use EightshiftLibs\Enqueue\Admin\EnqueueAdminCli;
use EightshiftLibs\Enqueue\Blocks\EnqueueBlocksCli;
use EightshiftLibs\Enqueue\Theme\EnqueueThemeCli;
use EightshiftLibs\Main\MainCli;
use EightshiftLibs\Manifest\ManifestCli;
use EightshiftLibs\Menu\MenuCli;

/**
 * Class CliInitAll
 */
class CliInitAll extends AbstractCli
{
	/**
	 * CLI command name
	 *
	 * @var string
	 */
	public const COMMAND_NAME = 'setup_all';

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return self::COMMAND_NAME;
	}

	/**
	 * Get WPCLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Generates initial setup for all service classes in the WordPress theme project.',
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs) // phpcs:ignore
	{
		if (!function_exists('\add_action')) {
			$this->runReset();
			\WP_CLI::log('--------------------------------------------------');
		}

		$classes = array_merge(
			Cli::CLASSES_LIST,
			Cli::PUBLIC_CLASSES,
			Cli::SETUP_CLASSES
		);

		$this->getEvalLoop($classes, true, $assocArgs);

		\WP_CLI::log('--------------------------------------------------');

		\WP_CLI::log((string)shell_exec('npm run start')); // phpcs:ignore

		\WP_CLI::log('--------------------------------------------------');

		\WP_CLI::success('All commands are finished.');
	}
}
