<?php

/**
 * Class that registers WP-CLI command for Service Example.
 *
 * @package EightshiftLibs\Services
 */

declare(strict_types=1);

namespace EightshiftLibs\Services;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliCreate;
use EightshiftLibs\Helpers\Helpers;

/**
 * Class ServiceExampleCli
 */
class ServiceExampleCli extends AbstractCli
{
	/**
	 * Template name.
	 */
	public const TEMPLATE = 'Service';

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
		return 'service-example';
	}

	/**
	 * Define default arguments.
	 *
	 * @return array<string, int|string|boolean>
	 */
	public function getDefaultArgs(): array
	{
		$sep = \DIRECTORY_SEPARATOR;

		return [
			'folder' => "TestFolder{$sep}Tmp",
			'file_name' => 'TestTest',
		];
	}

	/**
	 * Get WP-CLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Create empty generic service class.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'folder',
					'description' => 'The output folder path relative to src folder. Example: main or `main` or `config` or nested `main/config`',
					'optional' => false,
				],
				[
					'type' => 'assoc',
					'name' => 'file_name',
					'description' => 'The output file name. Example: Main',
					'optional' => false,
				],
			],
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Used to create generic service class to kickstart your custom service class.

				## EXAMPLES

				# Create service class:
				$ wp {$this->commandParentName} {$this->getCommandParentName()} {$this->getCommandName()} --folder='test' --file_name='Test'

				## RESOURCES

				Service class will be created from this example:
				https://github.com/infinum/eightshift-libs/blob/develop/src/Services/ServiceExample.php
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		$assocArgs = $this->prepareArgs($assocArgs);

		$this->getIntroText($assocArgs);

		// Get Props.
		$folder = $this->getArg($assocArgs, 'folder');
		$fileName = $this->prepareSlug($this->getArg($assocArgs, 'file_name'));

		// Get full class name.
		$className = $this->getClassShortName();
		$classNameNew = $this->getFileName($fileName);
		$ds = \DIRECTORY_SEPARATOR;

		// Create new namespace from the folder structure.
		$folderParts = \array_map(
			function ($item) {
				return \ucfirst($item);
			},
			\explode($ds, $folder)
		);

		$newNamespace = '\\' . \implode('\\', $folderParts);

		// Read the template contents, and replace the placeholders with provided variables.
		$this->getExampleTemplate(__DIR__, static::TEMPLATE)
			->searchReplaceString($className, $classNameNew)
			->renameGlobals($assocArgs)
			->searchReplaceString('\\Services;', "{$newNamespace};")
			->outputWrite(Helpers::getProjectPaths('src', $folder), "{$classNameNew}.php", $assocArgs);
	}
}
