<?php

/**
 * Class that registers WPCLI command for custom WPCLI command.
 *
 * @package EightshiftLibs\WpCli
 */

declare(strict_types=1);

namespace EightshiftLibs\WpCli;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class WpCli
 */
class WpCli extends AbstractCli
{
	/**
	 * Output dir relative path.
	 *
	 * @var string
	 */
	public const OUTPUT_DIR = 'src' . \DIRECTORY_SEPARATOR . 'WpCli';

	/**
	 * Get WPCLI command parent name
	 *
	 * @return string
	 */
	public function getCommandParentName(): string
	{
		return 'create';
	}

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'wp_cli';
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
					'optional' => \defined('ES_DEVELOP_MODE') ? \ES_DEVELOP_MODE : false
				],
			],
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		// Get Props.
		$commandName = $assocArgs['command_name'] ?? 'custom-command';

		// Get full class name.
		$className = $this->getFileName($commandName);
		$className = $className . $this->getClassShortName(true);

		// Read the template contents, and replace the placeholders with provided variables.
		$this->getExampleTemplate(__DIR__, $this->getClassShortName(true))
			->renameClassNameWithPrefix($this->getClassShortName(true), $className)
			->renameNamespace($assocArgs)
			->renameUse($assocArgs)
			->searchReplaceString('command_name', $commandName)
			->outputWrite(static::OUTPUT_DIR, $className, $assocArgs);
	}
}
