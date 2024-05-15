<?php

/**
 * Class that registers WP-CLI command initial setup of plugin project.
 *
 * @package EightshiftLibs\Cli
 */

declare(strict_types=1);

namespace EightshiftLibs\Init;

use EightshiftLibs\Cache\ManifestCacheCli;
use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliInit;
use EightshiftLibs\Config\ConfigPluginCli;
use EightshiftLibs\Main\MainCli;
use EightshiftLibs\Plugin\PluginCli;

/**
 * Class InitPluginCli
 */
class InitPluginCli extends AbstractCli
{
	/**
	 * All classes for initial plugin setup for project.
	 *
	 * @var array<int, mixed>
	 */
	public const COMMANDS = [
		ManifestCacheCli::class,
		ConfigPluginCli::class,
		MainCli::class,
		PluginCli::class,
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
		return 'plugin';
	}

	/**
	 * Get WP-CLI command doc.
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Kickstart your WordPress plugin with this simple command.',
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Generates initial plugin setup with all files to create a custom plugin.

				## EXAMPLES

				# Setup plugin:
				$ wp {$this->commandParentName} {$this->getCommandParentName()} {$this->getCommandName()}
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		$groupOutput = $assocArgs[self::ARG_GROUP_OUTPUT];

		$assocArgs = $this->prepareArgs($assocArgs);

		$this->getIntroText($assocArgs);

		foreach (static::COMMANDS as $item) {
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
				'All the files have been created, you can start working on your awesome plugin!',
				'success',
				\__('Ready to go!', 'eightshift-libs')
			);
			$this->getAssetsCommandText();
		}
	}
}
