<?php

/**
 * Class that registers WPCLI command initial setup of theme project.
 *
 * @package EightshiftLibs\Cli
 */

declare(strict_types=1);

namespace EightshiftLibs\Cli;

use EightshiftLibs\Blocks\BlocksCli;
use EightshiftLibs\Build\BuildCli;
use EightshiftLibs\CiExclude\CiExcludeCli;
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

/**
 * Class CliInitProject
 */
class CliInitProject extends AbstractCli
{

	/**
	 * All classes for initial theme setup for project
	 *
	 * @var array
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
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'setup_project';
	}

	/**
	 * Get WPCLI command doc.
	 *
	 * @return array
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Generates initial setup for WordPress theme project with all files to run a client project, for example: gitignore file for the full WordPress project, continuous integration exclude files, etc.',
		];
	}

	public function __invoke(array $args, array $assocArgs) // phpcs:ignore
	{
		if (!function_exists('\add_action')) {
			$this->runReset();
			\WP_CLI::log('--------------------------------------------------');
		}

		foreach (static::INIT_PROJECT_CLASSES as $item) {
			try {
				$reflectionClass = new \ReflectionClass($item);
			} catch (\ReflectionException $e) {
				exit("{$e->getCode()}: {$e->getMessage()}");
			}

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
