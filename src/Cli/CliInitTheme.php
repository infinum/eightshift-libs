<?php

/**
 * Class that registers WPCLI command initial setup of theme project.
 *
 * @package EightshiftLibs\Cli
 */

declare(strict_types=1);

namespace EightshiftLibs\Cli;

use EightshiftLibs\Blocks\BlocksCli;
use EightshiftLibs\Cli\ParentGroups\CliSetup;
use EightshiftLibs\Config\ConfigCli;
use EightshiftLibs\Enqueue\Admin\EnqueueAdminCli;
use EightshiftLibs\Enqueue\Blocks\EnqueueBlocksCli;
use EightshiftLibs\Enqueue\Theme\EnqueueThemeCli;
use EightshiftLibs\Main\MainCli;
use EightshiftLibs\Manifest\ManifestCli;
use EightshiftLibs\Menu\MenuCli;
use ReflectionClass;
use WP_CLI;

/**
 * Class CliInitTheme
 */
class CliInitTheme extends AbstractCli
{
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
		return 'theme';
	}

	/**
	 * Get WPCLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Kickstart your WordPress theme with this simple command.',
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Generates initial theme setup with all files to create a custom theme.

				## EXAMPLES

				# Setup theme:
				$ wp boilerplate {$this->getCommandParentName()} {$this->getCommandName()}
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		if (!\function_exists('\add_action')) {
			$this->runReset();
			WP_CLI::log('--------------------------------------------------');
		}

		foreach (static::INIT_THEME_CLASSES as $item) {
			$reflectionClass = new ReflectionClass($item);

			$class = $reflectionClass->newInstanceArgs([$this->commandParentName]);

			if (\method_exists($class, 'getCommandName') && \method_exists($class, 'getCommandParentName')) {
				if (\function_exists('\add_action')) {
					WP_CLI::runcommand("{$this->commandParentName} {$class->getCommandParentName()} {$class->getCommandName()} {$this->prepareArgsManual($assocArgs)}");
				} else {
					// phpcs:ignore Generic.Files.LineLength.TooLong
					WP_CLI::runcommand("eval-file bin" . \DIRECTORY_SEPARATOR . "cli.php {$class->getCommandParentName()}_{$class->getCommandName()} {$this->prepareArgsManual($assocArgs)} --skip-wordpress");
				}
			}
		}

		WP_CLI::log('--------------------------------------------------');

		if (!\getenv('ES_TEST')) {
			WP_CLI::log('We have copied everyting that you need in your active theme. Now make sure that you naviate inside and type:');
			WP_CLI::log(WP_CLI::colorize('%Mnpm run start%n'));
		}

		WP_CLI::log('--------------------------------------------------');

		WP_CLI::success('You are ready to start developing, good luck.');
	}
}
