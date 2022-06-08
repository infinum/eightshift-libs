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

/**
 * Class ConfigCli
 */
class ConfigCli extends AbstractCli
{
	/**
	 * Output dir relative path.
	 */
	public const OUTPUT_DIR = 'src' . \DIRECTORY_SEPARATOR . 'Config';

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
	 * Define default develop props.
	 *
	 * @param string[] $args WPCLI eval-file arguments.
	 *
	 * @return array<string, int|string|boolean>
	 */
	public function getDevelopArgs(array $args): array
	{
		return [
			'name' => 'Boilerplate',
			'version' => '1',
			'routes_version' => '2',
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
			'name' => 'Boilerplate',
			'version' => '1',
			'routes_version' => '2',
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
				$ wp boilerplate {$this->getCommandParentName()} {$this->getCommandName()}

				## RESOURCES

				Service class will be created from this example:
				https://github.com/infinum/eightshift-libs/blob/develop/src/Config/ConfigExample.php
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
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
			$class->searchReplaceString('eightshift-libs', $name);
		}

		if (!empty($version)) {
			$class->searchReplaceString('1.0.0', $version);
		}

		if (!empty($routesVersion)) {
			$class->searchReplaceString('v1', $routesVersion);
		}

		// Output final class to new file/folder and finish.
		$class->outputWrite(static::OUTPUT_DIR, $className, $assocArgs);
	}
}
