<?php

/**
 * Class that registers WPCLI command for GitIgnoreCli.
 *
 * @package EightshiftLibs\GitIgnore
 */

declare(strict_types=1);

namespace EightshiftLibs\GitIgnore;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliCreate;
use EightshiftLibs\Helpers\Helpers;

/**
 * Class GitIgnoreCli
 */
class GitIgnoreCli extends AbstractCli
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
		return 'gitignore';
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
			'shortdesc' => 'Copy .gitignore file for excluding unnecessary files from git. This file will be copied to WordPress root folder.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'path',
					'description' => "Define absolute folder path where `.gitignore` file will be created.",
					'optional' => true,
					'default' => $this->getDefaultArg('path'),
				],
			],
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Used to list all files you want to exclude from GitHub repo or any other versioning tool.
				This file will be copied to your project root folder.

				## EXAMPLES

				# Copy file:
				$ wp {$this->commandParentName} {$this->getCommandParentName()} {$this->getCommandName()}

				## RESOURCES

				File will be created from this example:
				https://github.com/infinum/eightshift-libs/blob/develop/src/GitIgnore/GitIgnoreExample.php
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		$assocArgs = $this->prepareArgs($assocArgs);

		$this->getIntroText($assocArgs);

		// Get full class name.
		$className = $this->getClassShortName();

		// Get Props.
		$path = $this->getArg($assocArgs, 'path');

		// Read the template contents, and replace the placeholders with provided variables.
		$this->getExampleTemplate(__DIR__, $className)
			->renameGlobals($assocArgs)
			->outputWrite($path, '.gitignore', $assocArgs);
	}
}
