<?php

/**
 * Class that registers WPCLI command initial setup of theme project.
 *
 * @package EightshiftLibs\Init
 */

declare(strict_types=1);

namespace EightshiftLibs\Init;

use EightshiftLibs\Blocks\BlocksCli;
use EightshiftLibs\Build\BuildCli;
use EightshiftLibs\CiExclude\CiExcludeCli;
use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliInit;
use EightshiftLibs\Config\ConfigCli;
use EightshiftLibs\ConfigProject\ConfigProjectCli;
use EightshiftLibs\Enqueue\Admin\EnqueueAdminCli;
use EightshiftLibs\Enqueue\Blocks\EnqueueBlocksCli;
use EightshiftLibs\Enqueue\Theme\EnqueueThemeCli;
use EightshiftLibs\GitIgnore\GitIgnoreCli;
use EightshiftLibs\Main\MainCli;
use EightshiftLibs\Manifest\ManifestCli;
use EightshiftLibs\Menu\MenuCli;
use EightshiftLibs\Readme\ReadmeCli;
use EightshiftLibs\Setup\SetupCli;
use ReflectionClass;
use WP_CLI;

/**
 * Class InitProjectCli
 */
class InitProjectCli extends AbstractCli
{
	/**
	 * All classes for initial theme setup for project
	 *
	 * @var class-string[]
	 */
	public const INIT_PROJECT_CLASSES = [
		ConfigCli::class,
		MainCli::class,
		ManifestCli::class,
		EnqueueAdminCli::class,
		EnqueueBlocksCli::class,
		EnqueueThemeCli::class,
		MenuCli::class,
		BlocksCli::class,
		GitIgnoreCli::class,
		SetupCli::class,
		CiExcludeCli::class,
		BuildCli::class,
		ReadmeCli::class,
		ConfigProjectCli::class,
	];

	/**
	 * Get WPCLI command parent name
	 *
	 * @return string
	 */
	public function getCommandParentName(): string
	{
		return CliInit::COMMAND_NAME;
	}

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'project';
	}

	/**
	 * Get WPCLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Kickstart your WordPress project with this simple command.',
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Generates initial project setup with all files to run on the client project.
				For example: gitignore file for the full WordPress project, continuous integration exclude files, etc.

				## EXAMPLES

				# Setup project:
				$ wp boilerplate {$this->getCommandParentName()} {$this->getCommandName()}
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		foreach (static::INIT_PROJECT_CLASSES as $item) {
			$reflectionClass = new ReflectionClass($item);

			$class = $reflectionClass->newInstanceArgs([$this->commandParentName]);

			if (\method_exists($class, 'getCommandName') && \method_exists($class, 'getCommandParentName')) {
				WP_CLI::runcommand("{$this->commandParentName} {$class->getCommandParentName()} {$class->getCommandName()} {$this->prepareArgsManual($assocArgs)}");
			}
		}

		WP_CLI::log('--------------------------------------------------');

		if (!\getenv('ES_TEST')) {
			WP_CLI::log((string)shell_exec('npm run start')); // phpcs:ignore
		}

		WP_CLI::log('--------------------------------------------------');

		WP_CLI::success('All commands are finished.');
	}
}
