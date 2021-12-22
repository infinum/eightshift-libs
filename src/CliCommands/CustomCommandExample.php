<?php

/**
 * The CustomCommandExample specific functionality.
 *
 * @package EightshiftLibs\CliCommands
 */

declare(strict_types=1);

namespace EightshiftBoilerplate\CliCommands;

use EightshiftLibs\CliCommands\AbstractCustomCommand;

/**
 * Class CustomCommandExample
 */
class CustomCommandExample extends AbstractCustomCommand
{
	/**
	 * CLI command name
	 *
	 * @var string
	 */
	public const COMMAND_NAME = 'command_name';

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return self::COMMAND_NAME;
	}

	/**
	 * Get WPCLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Generates custom WPCLI command in your project.'
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs) // phpcs:ignore
	{
		// Place your logic here.
	}
}
