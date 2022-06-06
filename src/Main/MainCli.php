<?php

/**
 * Class that registers WPCLI command for Main.
 *
 * @package EightshiftLibs\Main
 */

declare(strict_types=1);

namespace EightshiftLibs\Main;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliCreate;

/**
 * Class MainCli
 */
class MainCli extends AbstractCli
{
	/**
	 * Output dir relative path.
	 */
	public const OUTPUT_DIR = 'src' . \DIRECTORY_SEPARATOR . 'Main';

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
		return 'main';
	}

	/**
	 * Get WPCLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Create Main class file for all other features using service container pattern.',
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Used to create main entry point service class that activates all other classes and initializes autowiring.
				Without this class nothing will work.

				## EXAMPLES

				# Create service class:
				$ wp boilerplate {$this->getCommandParentName()} {$this->getCommandName()}

				## RESOURCES

				Service class will be created from this example:
				https://github.com/infinum/eightshift-libs/blob/develop/src/Main/MainExample.php
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		// Read the template contents, and replace the placeholders with provided variables.
		$this->getExampleTemplate(__DIR__, $this->getClassShortName())
			->renameClassName($this->getClassShortName())
			->renameNamespace($assocArgs)
			->renameUse($assocArgs)
			->outputWrite(static::OUTPUT_DIR, $this->getClassShortName(), $assocArgs);
	}
}
