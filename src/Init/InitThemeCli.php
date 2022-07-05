<?php

/**
 * Class that registers WPCLI command initial setup of theme project.
 *
 * @package EightshiftLibs\Cli
 */

declare(strict_types=1);

namespace EightshiftLibs\Init;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliInit;
use EightshiftLibs\Config\ConfigCli;
use EightshiftLibs\Enqueue\Admin\EnqueueAdminCli;
use EightshiftLibs\Enqueue\Blocks\EnqueueBlocksCli;
use EightshiftLibs\Enqueue\Theme\EnqueueThemeCli;
use EightshiftLibs\Main\MainCli;
use EightshiftLibs\Manifest\ManifestCli;
use EightshiftLibs\Menu\MenuCli;
use ReflectionClass;

/**
 * Class InitThemeCli
 */
class InitThemeCli extends AbstractCli
{
	/**
	 * All classes for initial theme setup for project
	 *
	 * @var class-string[]
	 */
	public const COMMANDS = [
		'service classes' => [
			ConfigCli::class,
			MainCli::class,
			ManifestCli::class,
			EnqueueAdminCli::class,
			EnqueueBlocksCli::class,
			EnqueueThemeCli::class,
			MenuCli::class,
		],
		'block editor files' => [
			InitBlocksCli::class,
		]
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
		foreach (static::COMMANDS as $parentName => $parentClasses) {
			$this->cliLog(sprintf('Setting theme %s:', $parentName), 'C');

			foreach ($parentClasses as $className) {
				$reflectionClass = new ReflectionClass($className);
				$class = $reflectionClass->newInstanceArgs([$this->commandParentName]);

				$class->__invoke([], [
					'groupOutput' => true,
				]);
			}

			$this->cliLog('--------------------------------------------------');
		}
	}
}
