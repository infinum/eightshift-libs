<?php

/**
 * Class that registers WPCLI command for Blocks.
 *
 * @package EightshiftLibs\Enqueue\Theme
 */

declare(strict_types=1);

namespace EightshiftLibs\Enqueue\Theme;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliCreate;
use EightshiftLibs\Helpers\Components;

/**
 * Class EnqueueThemeCli
 */
class EnqueueThemeCli extends AbstractCli
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
		return 'enqueue_theme';
	}

	/**
	 * Get WPCLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Create enqueue theme service class.',
			'longdesc' => $this->prepareLongDesc("
			## USAGE

			Used to create enqueue service class to register all theme styles and scripts.

			## EXAMPLES

			# Create service class:
			$ wp boilerplate {$this->getCommandParentName()} {$this->getCommandName()}

			## RESOURCES

			Service class will be created from this example:
			https://github.com/infinum/eightshift-libs/blob/develop/src/Enqueue/Theme/EnqueueThemeExample.php
		"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		$className = $this->getClassShortName();

		// Read the template contents, and replace the placeholders with provided variables.
		$this->getExampleTemplate(__DIR__, $className)
			->renameClassName($className)
			->renameNamespace($assocArgs)
			->renameUse($assocArgs)
			->outputWrite(Components::getProjectPaths('srcDestination', 'Enqueue' . \DIRECTORY_SEPARATOR . 'Theme'), "{$className}.php", $assocArgs);
	}
}
