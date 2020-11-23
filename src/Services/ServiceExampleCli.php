<?php

/**
 * Class that registers WPCLI command for Service Example.
 *
 * @package EightshiftLibs\Services
 */

declare(strict_types=1);

namespace EightshiftLibs\Services;

use EightshiftLibs\Cli\AbstractCli;

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
	 * Define default develop props.
	 *
	 * @param array $args WPCLI eval-file arguments.
	 *
	 * @return array
	 */
	public function getDevelopArgs(array $args): array
	{
		return [
			'folder' => $args[1] ?? 'TestFolder/TMP',
			'file_name' => $args[2] ?? 'TestTest',
		];
	}

	/**
	 * Get WPCLI command doc.
	 *
	 * @return array
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Generates empty generic service class.',
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
		];
	}

	public function __invoke(array $args, array $assocArgs) // phpcs:ignore
	{
		// Get Props.
		$folder = $assocArgs['folder'];
		$fileName = $this->prepareSlug($assocArgs['file_name']);

		// Get full class name.
		$className = $this->getClassShortName();
		$classNameNew = $this->getFileName($fileName);

		// Read the template contents, and replace the placeholders with provided variables.
		$class = $this->getExampleTemplate(__DIR__, static::TEMPLATE);

		// Replace stuff in file.
		$class = str_replace($className, $classNameNew, $class);

		$class = $this->renameNamespace($assocArgs, $class);

		$class = $this->renameUse($assocArgs, $class);

		// Create new namespace from folder structure.
		$folderParts = array_map(
			function ($item) {
				return ucfirst($item);
			},
			explode('/', $folder)
		);

		$newNamespace = '\\' . implode('\\', $folderParts);
		$class = str_replace('\\Services;', "{$newNamespace};", $class);

		// Output final class to new file/folder and finish.
		$this->outputWrite(static::OUTPUT_DIR . '/' . $folder, $classNameNew, $class, $assocArgs);
	}
}
