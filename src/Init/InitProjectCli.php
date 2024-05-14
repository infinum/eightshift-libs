<?php

/**
 * Class that registers WP-CLI command initial setup of theme project.
 *
 * @package EightshiftLibs\Init
 */

declare(strict_types=1);

namespace EightshiftLibs\Init;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliInit;
use EightshiftLibs\ConfigProject\ConfigProjectCli;
use EightshiftLibs\GitIgnore\GitIgnoreCli;
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
	 * All classes for initial theme setup for project.
	 *
	 * @var array<int, mixed>
	 */
	public const COMMANDS = [
		InitThemeCli::class,
		GitIgnoreCli::class,
		SetupCli::class,
		ReadmeCli::class,
		ConfigProjectCli::class,
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
		return 'project';
	}

	/**
	 * Get WP-CLI command doc.
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
			$reflectionClass = new ReflectionClass($item);
			$class = $reflectionClass->newInstanceArgs([$this->commandParentName]);

			$class->__invoke([], $assocArgs);
		}

		if (!$groupOutput) {
			WP_CLI::runcommand("eval 'shell_exec(\"npm run build\");'");
			$this->cliLog('We have moved everything you need to start creating your awesome WordPress project.', "M");
			$this->cliLog('Happy developing!', "M");
		}
	}
}
