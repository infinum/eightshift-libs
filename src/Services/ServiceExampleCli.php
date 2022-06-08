<?php

/**
 * Class that registers WPCLI command for Service Example.
 *
 * @package EightshiftLibs\Services
 */

declare(strict_types=1);

namespace EightshiftLibs\Services;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliCreate;

/**
 * Class ServiceExampleCli
 */
class ServiceExampleCli extends AbstractCli
{
	/**
	 * Output dir relative path.
	 */
	public const OUTPUT_DIR = 'src';

	/**
	 * Template name.
	 */
	public const TEMPLATE = 'Service';

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
		return 'service_example';
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
			'folder' => 'TestFolder/TMP',
			'file_name' => 'TestTest',
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
			'folder' => 'TestFolder/TMP',
			'file_name' => 'TestTest',
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
				$ wp boilerplate {$this->getCommandParentName()} {$this->getCommandName()} --folder='test' --file_name='Test'

				## RESOURCES

				Service class will be created from this example:
				https://github.com/infinum/eightshift-libs/blob/develop/src/Services/ServiceExample.php
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
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
			->renameNamespace($assocArgs)
			->renameUse($assocArgs)
			->searchReplaceString('\\Services;', "{$newNamespace};")
			->outputWrite(static::OUTPUT_DIR . $ds . $folder, $classNameNew, $assocArgs);
	}
}
