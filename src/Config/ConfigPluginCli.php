<?php

/**
 * Class that registers WP-CLI command for Config Plugin.
 *
 * @package EightshiftLibs\Config
 */

declare(strict_types=1);

namespace EightshiftLibs\Config;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliCreate;
use EightshiftLibs\Helpers\Helpers;

/**
 * Class ConfigPluginCli
 */
class ConfigPluginCli extends AbstractCli
{
	/**
	 * Get WP-CLI command parent name
	 *
	 * @return string
	 */
	public function getCommandParentName(): string
	{
		return CliCreate::COMMAND_NAME;
	}

	/**
	 * Get WP-CLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'config-plugin';
	}

	/**
	 * Define default arguments.
	 *
	 * @return array<string, int|string|boolean>
	 */
	public function getDefaultArgs(): array
	{
		return [];
	}

	/**
	 * Get WP-CLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Create plugin config service class.',
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Used to create plugin config class with settings like project name, version, REST-API name/version, etc.

				## EXAMPLES

				# Create service class:
				$ wp {$this->commandParentName} {$this->getCommandParentName()} {$this->getCommandName()}

				## RESOURCES

				Service class will be created from this example:
				https://github.com/infinum/eightshift-libs/blob/develop/src/Config/ConfigPluginExample.php
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		$assocArgs = $this->prepareArgs($assocArgs);

		$this->getIntroText($assocArgs);

		$className = $this->getClassShortName();
		$newName = 'Config';

		// Read the template contents, and replace the placeholders with provided variables.
		$class = $this->getExampleTemplate(__DIR__, $className)
			->renameClassNameWithPrefix($className, $newName)
			->renameGlobals($assocArgs);

		// Output final class to new file/folder and finish.
		$class->outputWrite(Helpers::getProjectPaths('src', 'Config'), "{$newName}.php", $assocArgs);
	}
}
