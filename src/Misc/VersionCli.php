<?php

/**
 * Class that registers WPCLI command for Version.
 *
 * @package EightshiftLibs\Misc
 */

declare(strict_types=1);

namespace EightshiftLibs\Misc;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliRun;
use EightshiftLibs\Helpers\Components;

/**
 * Class VersionCli
 */
class VersionCli extends AbstractCli
{
	/**
	 * Get WPCLI command parent name
	 *
	 * @return string
	 */
	public function getCommandParentName(): string
	{
		return CliRun::COMMAND_NAME;
	}

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'version';
	}

	/**
	 * Define default arguments.
	 *
	 * @return array<string, int|string|boolean>
	 */
	public function getDefaultArgs(): array
	{
		return [
			'version' => '1.0.0',
		];
	}

	/**
	 * Get WPCLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Change projects version number.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'version',
					'description' => 'Set new version number.',
					'optional' => true,
					'default' => $this->getDefaultArg('version'),
				],
			],
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Used to update projects version number.

				## EXAMPLES

				# Update project version:
				$ wp {$this->commandParentName} {$this->getCommandParentName()} {$this->getCommandName()}

				## RESOURCES

				Command will be run using this code:
				https://github.com/infinum/eightshift-libs/blob/develop/src/Misc/VersionCli.php
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		$this->getIntroText($assocArgs);

		// Get Props.
		$version = $this->getArg($assocArgs, 'version');

		$assocArgs['skip_existing'] = 'true';
		// translators: %s is replaced with the new version number.
		$assocArgs['actionOutput'] = \sprintf(\__("version number changed to %s.", 'eightshift-libs'), $version);

		$path = Components::getProjectPaths('cliOutput');

		$sep = \DIRECTORY_SEPARATOR;
		$pluginName = \explode($sep, \rtrim($path, $sep));

		$files = [
			'package.json',
			'style.css',
			'functions.php',
			\end($pluginName) . '.php',
		];

		foreach ($files as $file) {
			if (\file_exists(Components::getProjectPaths('cliOutput', $file))) {
				$this->getExampleTemplate($path, $file, true)
				->renameVersionString($version)
				->outputWrite(Components::getProjectPaths('cliOutput'), $file, $assocArgs);
			}
		}
	}
}
