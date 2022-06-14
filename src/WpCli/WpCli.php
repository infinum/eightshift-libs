<?php

/**
 * Class that registers WPCLI command for custom WPCLI command.
 *
 * @package EightshiftLibs\WpCli
 */

declare(strict_types=1);

namespace EightshiftLibs\WpCli;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliCreate;

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
		return CliCreate::COMMAND_NAME;
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
	 * @return array<string, int|string|boolean>
	 */
	public function getDevelopArgs(array $args): array
	{
		return [
			'command_name' => 'test',
		];
	}

	/**
	 * Define default arguments.
	 *
	 * @return array<string, int|string|boolean>
	 */
	public function getDefaultArgs(): array
	{
		return [
			'command_name' => 'test',
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
			'shortdesc' => 'Create custom WPCLI command service class.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'command_name',
					'description' => 'The name of cli command name. Example: command_name.',
					'optional' => false,
				],
			],
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Used to create generic WP-CLI service class to kickstart your custom WP-CLI command.

				## EXAMPLES

				# Create service class:
				$ wp boilerplate {$this->getCommandParentName()} {$this->getCommandName()} --command_name='test'

				## RESOURCES

				Service class will be created from this example:
				https://github.com/infinum/eightshift-libs/blob/develop/src/WpCli/WpCliExample.php

				WP-CLI custom command documentation:
				https://make.wordpress.org/cli/handbook/guides/commands-cookbook/
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		// Get Props.
		$commandName = $this->getArg($assocArgs, 'command_name');

		// Get full class name.
		$className = $this->getFileName($commandName);
		$className = $className . $this->getClassShortName(true);

		// Read the template contents, and replace the placeholders with provided variables.
		$this->getExampleTemplate(__DIR__, $this->getClassShortName(true))
			->renameClassNameWithPrefix($this->getClassShortName(true), $className)
			->renameNamespace($assocArgs)
			->renameUse($assocArgs)
			->searchReplaceString($this->getArgTemplate('command_name'), $commandName)
			->outputWrite(static::OUTPUT_DIR, $className, $assocArgs);
	}
}
