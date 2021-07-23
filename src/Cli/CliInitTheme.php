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
 * Class CliInitTheme
 */
class CliInitTheme extends AbstractCli
{
	public const COMMAND_NAME = 'setup_theme';

	/**
	 * All classes for initial theme setup for project
	 *
	 * @var class-string[]
	 */
	public const INIT_THEME_CLASSES = [
		ConfigCli::class,
		MainCli::class,
		ManifestCli::class,
		EnqueueAdminCli::class,
		EnqueueBlocksCli::class,
		EnqueueThemeCli::class,
		MenuCli::class,
		BlocksCli::class,
	];

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
			'shortdesc' => 'Generates initial setup for WordPress theme project.',
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs) // phpcs:ignore
	{
		if (!function_exists('\add_action')) {
			$this->runReset();
			\WP_CLI::log('--------------------------------------------------');
		}

		foreach (static::INIT_THEME_CLASSES as $item) {
			try {
				$reflectionClass = new \ReflectionClass($item);
				// @codeCoverageIgnoreStart
				// See the explanation in the CliInitProject.
			} catch (\ReflectionException $e) {
				CliHelpers::cliError("{$e->getCode()}: {$e->getMessage()}");
			}
			// @codeCoverageIgnoreEnd

			$class = $reflectionClass->newInstanceArgs([$this->commandParentName]);

			if (method_exists($class, 'getCommandName')) {
				if (function_exists('\add_action')) {
					\WP_CLI::runcommand("{$this->commandParentName} {$class->getCommandName()} {$this->prepareArgsManual($assocArgs)}");
				} else {
					\WP_CLI::runcommand("eval-file bin/cli.php {$class->getCommandName()} {$this->prepareArgsManual($assocArgs)} --skip-wordpress");
				}
			}
		}

		\WP_CLI::log('--------------------------------------------------');

		\WP_CLI::log((string)shell_exec('npm run start')); // phpcs:ignore

		\WP_CLI::log('--------------------------------------------------');

		\WP_CLI::success('All commands are finished.');
	}
}
