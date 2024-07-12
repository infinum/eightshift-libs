<?php

/**
 * Class that registers WP-CLI command initial setup of theme project.
 *
 * @package EightshiftLibs\Init
 */

declare(strict_types=1);

namespace EightshiftLibs\Init;

use EightshiftLibs\AdminMenus\AdminPatternsHeaderFooterMenu\AdminPatternsHeaderFooterMenuCli;
use EightshiftLibs\AdminMenus\AdminPatternsMenu\AdminPatternsMenuCli;
use EightshiftLibs\AdminMenus\AdminThemeOptionsMenu\AdminThemeOptionsMenuCli;
use EightshiftLibs\Cache\ManifestCacheCli;
use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliInit;
use EightshiftLibs\Config\ConfigThemeCli;
use EightshiftLibs\Enqueue\Admin\EnqueueAdminCli;
use EightshiftLibs\Enqueue\Blocks\EnqueueBlocksCli;
use EightshiftLibs\Enqueue\Theme\EnqueueThemeCli;
use EightshiftLibs\Main\MainCli;
use EightshiftLibs\ThemeOptions\ThemeOptionsCli;

/**
 * Class InitThemeCli
 */
class InitThemeCli extends AbstractCli
{
	/**
	 * All classes for initial theme setup for project.
	 *
	 * @var array<string, array<string>>
	 */
	public const COMMANDS = [
		'common' => [
			ManifestCacheCli::class,
			ConfigThemeCli::class,
			MainCli::class,
			EnqueueAdminCli::class,
			EnqueueBlocksCli::class,
			EnqueueThemeCli::class,
			AdminPatternsMenuCli::class,
			InitBlocksCli::class,
		],
		'standard' => [
			AdminPatternsHeaderFooterMenuCli::class,
		],
		'tailwind' => [
			AdminThemeOptionsMenuCli::class,
			ThemeOptionsCli::class,
		],
	];

	/**
	 * Get WP-CLI command parent name.
	 *
	 * @return string
	 */
	public function getCommandParentName(): string
	{
		return CliInit::COMMAND_NAME;
	}

	/**
	 * Get WP-CLI command name.
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'theme';
	}

	/**
	 * Define default arguments.
	 *
	 * @return array<string, int|string|boolean>
	 */
	public function getDefaultArgs(): array
	{
		return [];
	}

	/**
	 * Get WP-CLI command doc.
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

				# Setup theme files:
				$ wp {$this->commandParentName} {$this->getCommandParentName()} {$this->getCommandName()}
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		$assocArgs = $this->prepareArgs($assocArgs);

		$this->getIntroText($assocArgs);

		$commands = [
			...($this->isTailwind() ? static::COMMANDS['tailwind'] : static::COMMANDS['standard']),
			...static::COMMANDS['common'],
		];

		foreach ($commands as $item) {
			$this->runCliCommand(
				$item,
				$this->commandParentName,
				\array_merge(
					$assocArgs,
					[
						self::ARG_GROUP_OUTPUT => true,
					]
				)
			);
		}

		if (!$assocArgs[self::ARG_GROUP_OUTPUT]) {
			$this->cliLogAlert(
				'All the files have been created, you can start working on your awesome theme!',
				'success',
				'Ready to go!'
			);
			$this->getAssetsCommandText();
		}
	}
}
