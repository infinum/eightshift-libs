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
use EightshiftLibs\Helpers\Components;

/**
 * Class WpCli
 */
class WpCli extends AbstractCli
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
		return 'wp-cli';
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
				$ wp {$this->commandParentName} {$this->getCommandParentName()} {$this->getCommandName()} --command_name='test'

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
		$this->getIntroText($assocArgs);

		// Get Props.
		$commandName = $this->getArg($assocArgs, 'command_name');

		// Get full class name.
		$className = $this->getFileName($commandName);
		$className = $className . $this->getClassShortName(true);

		// Read the template contents, and replace the placeholders with provided variables.
		$this->getExampleTemplate(__DIR__, $this->getClassShortName(true))
			->renameClassNameWithPrefix($this->getClassShortName(true), $className)
			->renameGlobals($assocArgs)
			->searchReplaceString($this->getArgTemplate('command_name'), $commandName)
			->outputWrite(Components::getProjectPaths('srcDestination', 'WpCli'), "{$className}.php", $assocArgs);
	}
}
