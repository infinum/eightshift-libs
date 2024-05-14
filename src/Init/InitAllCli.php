<?php

/**
 * Class that registers WPCLI command initial setup for all.
 *
 * @package EightshiftLibs\Init
 */

declare(strict_types=1);

namespace EightshiftLibs\Init;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\Cli;
use EightshiftLibs\Cli\ParentGroups\CliInit;
use ReflectionClass;

/**
 * Class InitAllCli
 */
class InitAllCli extends AbstractCli
{
	/**
	 * Get WPCLI command parent name.
	 *
	 * @return string
	 */
	public function getCommandParentName(): string
	{
		return CliInit::COMMAND_NAME;
	}

	/**
	 * Get WPCLI command name.
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'all';
	}

	/**
	 * Get WPCLI command doc.
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Move everything to your project.',
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				This command is used to move everything that we have to your project, all service classes, block editor items, etc.

				## EXAMPLES

				# Setup project:
				$ wp {$this->commandParentName} {$this->getCommandParentName()} {$this->getCommandName()}
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		$assocArgs = $this->prepareArgs($assocArgs);
		$groupOutput = $assocArgs[self::ARG_GROUP_OUTPUT];

		$this->getIntroText($assocArgs);

		$commands = array_merge(
			Cli::CREATE_COMMANDS,
			[
				InitBlocksCli::class,
			]
			);

		foreach ($commands as $item) {
			$reflectionClass = new ReflectionClass($item);
			$class = $reflectionClass->newInstanceArgs([$this->commandParentName]);

			$class->__invoke([], $assocArgs);
		}

		if (!$groupOutput) {
			$this->cliLog('We have moved everything we have to your project. Please type `npm start` in your terminal to kickstart your assets bundle process.', "M");
			$this->cliLog('Happy developing!', "M");
		}
	}
}
