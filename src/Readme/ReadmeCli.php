<?php

/**
 * Class that registers WPCLI command for ReadmeCli.
 *
 * @package EightshiftLibs\Readme
 */

declare(strict_types=1);

namespace EightshiftLibs\Readme;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliCreate;
use EightshiftLibs\Helpers\Helpers;

/**
 * Class ReadmeCli
 */
class ReadmeCli extends AbstractCli
{
	/**
	 * Get WPCLI command parent name
	 *
	 * @return string
	 */
	public function getCommandParentName(): string
	{
		return CliCreate::COMMAND_NAME;
	}

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'readme';
	}

	/**
	 * Define default arguments.
	 *
	 * @return array<string, int|string|boolean>
	 */
	public function getDefaultArgs(): array
	{
		return [
			'path' => Helpers::getProjectPaths('projectRoot'),
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
			'shortdesc' => 'Copy readme.md file for documentation.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'path',
					'description' => 'Define absolute folder path where readme file will be created.',
					'optional' => true,
					'default' => $this->getDefaultArg('path'),
				],
			],
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Used to document your project to help yourself and other people.
				This file will be copied to your project root folder.

				## EXAMPLES

				# Copy file:
				$ wp {$this->commandParentName} {$this->getCommandParentName()} {$this->getCommandName()}

				## RESOURCES

				File will be created from this example:
				https://github.com/infinum/eightshift-libs/blob/develop/src/Readme/README.md
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		$assocArgs = $this->prepareArgs($assocArgs);

		$this->getIntroText($assocArgs);

		// Get Props.
		$path = $this->getArg($assocArgs, 'path');

		// Read the template contents, and replace the placeholders with provided variables.
		$this->getExampleTemplate(__DIR__, 'README.md')
			->renameGlobals($assocArgs)
			->outputWrite($path, 'README.md', $assocArgs);
	}
}
