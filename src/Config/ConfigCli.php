<?php

/**
 * Class that registers WPCLI command for Config.
 *
 * @package EightshiftLibs\Config
 */

declare(strict_types=1);

namespace EightshiftLibs\Config;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliCreate;
use EightshiftLibs\Helpers\Components;

/**
 * Class ConfigCli
 */
class ConfigCli extends AbstractCli
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
		return 'config';
	}

	/**
	 * Define default arguments.
	 *
	 * @return array<string, int|string|boolean>
	 */
	public function getDefaultArgs(): array
	{
		return [
			'name' => 'Boilerplate',
			'version' => '1',
			'routes_version' => '1',
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
			'shortdesc' => 'Create project config service class.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'name',
					'description' => 'Define project name.',
					'optional' => true,
					'default' => $this->getDefaultArg('name'),
				],
				[
					'type' => 'assoc',
					'name' => 'version',
					'description' => 'Define project version.',
					'optional' => true,
					'default' => $this->getDefaultArg('version'),
				],
				[
					'type' => 'assoc',
					'name' => 'routes_version',
					'description' => 'Define project REST version.',
					'optional' => true,
					'default' => $this->getDefaultArg('routes_version'),
				],
			],
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Used to create project config class with settings like project name, version, REST-API name/version, etc.

				## EXAMPLES

				# Create service class:
				$ wp {$this->commandParentName} {$this->getCommandParentName()} {$this->getCommandName()}

				## RESOURCES

				Service class will be created from this example:
				https://github.com/infinum/eightshift-libs/blob/develop/src/Config/ConfigExample.php
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		$this->getIntroText($assocArgs);

		// Get Props.
		$name = $this->getArg($assocArgs, 'name');
		$version = $this->getArg($assocArgs, 'version');
		$routesVersion = $this->getArg($assocArgs, 'routes_version');

		$className = $this->getClassShortName();

		// Read the template contents, and replace the placeholders with provided variables.
		$class = $this->getExampleTemplate(__DIR__, $className)
			->renameClassName($className)
			->renameNamespace($assocArgs)
			->renameUse($assocArgs);

		if (!empty($name)) {
			$class->searchReplaceString($this->getArgTemplate('name'), $name);
		}

		if (!empty($version)) {
			$class->searchReplaceString($this->getArgTemplate('version'), $version);
		}

		if (!empty($routesVersion)) {
			$class->searchReplaceString($this->getArgTemplate('routes_version'), $routesVersion);
		}

		// Output final class to new file/folder and finish.
		$class->outputWrite(Components::getProjectPaths('srcDestination', 'Config'), "{$className}.php", $assocArgs);
	}
}
