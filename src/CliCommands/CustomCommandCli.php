<?php

/**
 * Class that registers WPCLI command for custom WPCLI command.
 *
 * @package EightshiftLibs\CliCommand
 */

declare(strict_types=1);

namespace EightshiftLibs\CliCommands;

use EightshiftLibs\Cli\AbstractCli;
use WP_CLI;

/**
 * Class CustomCommandCli
 */
class CustomCommandCli extends AbstractCli
{
	/**
	 * CLI command name
	 *
	 * @var string
	 */
	public const COMMAND_NAME = 'create_cli_command';

	/**
	 * Output dir relative path.
	 *
	 * @var string
	 */
	public const OUTPUT_DIR = 'src' . \DIRECTORY_SEPARATOR . 'CliCommands';

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
	 * Define default develop props.
	 *
	 * @param string[] $args WPCLI eval-file arguments.
	 *
	 * @return array<string, mixed>
	 */
	public function getDevelopArgs(array $args): array
	{
		return [
			'command_name' => $args[1] ?? 'test',
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
			'shortdesc' => 'Generates custom WPCLI command in your project.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'command_name',
					'description' => 'The name of cli command name. Example: command_name.',
					'optional' => \defined('ES_DEVELOP_MODE') ?? false
				],
			],
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		// Get Props.
		$commandName = $this->prepareSlug($assocArgs['command_name'] ?? 'custom-command');

		// Get full class name.
		$className = $this->getFileName($commandName);
		$className = $className . $this->getClassShortName();

		// If slug is empty throw error.
		if (empty($commandName)) {
			WP_CLI::error("Empty command name provided, please set the command name using --command_name=\"command-name\"");
		}

		// Read the template contents, and replace the placeholders with provided variables.
		$this->getExampleTemplate(__DIR__, $this->getClassShortName())
			->renameClassNameWithPrefix($this->getClassShortName(), $className)
			->renameNamespace($assocArgs)
			->renameUse($assocArgs)
			->searchReplaceString('command_name', "{$commandName}")
			->outputWrite(static::OUTPUT_DIR, $className, $assocArgs);
	}
}
