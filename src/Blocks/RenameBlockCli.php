<?php

/**
 * Class that registers WPCLI command for Blocks Block.
 *
 * @package EightshiftLibs\Blocks
 */

declare(strict_types=1);

namespace EightshiftLibs\Blocks;

use EightshiftLibs\Cli\ParentGroups\CliBlocks;
use EightshiftLibs\Helpers\Components;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use WP_CLI;

/**
 * Class RenameBlockCli
 */
class RenameBlockCli extends AbstractBlocksCli
{
	/**
	 * Get WPCLI command parent name
	 *
	 * @return string
	 */
	public function getCommandParentName(): string
	{
		return CliBlocks::COMMAND_NAME;
	}

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'rename';
	}

	/**
	 * Define default arguments.
	 *
	 * @return array<string, int|string|boolean>
	 */
	public function getDefaultArgs(): array
	{
		return [
			'name' => 'example-1',
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
			'shortdesc' => 'Create new block inside your project.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'name',
					'description' => 'Specify a new block name.',
					'optional' => false,
				],
				[
					'type' => 'assoc',
					'name' => 'component',
					'description' => 'Specify if copied folder should be component.',
					'optional' => true,
					'options' => [
						'true',
					]
				],
			],
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Used to copy and rename dummy block from our library to your project. After copying script will rename folders, files and their content with provided block name.

				## EXAMPLES

				# Rename dummy block by name:
				$ wp {$this->commandParentName} {$this->getCommandParentName()} {$this->getCommandName()} --name='hero'

				# Rename dummy component by name:
				$ wp {$this->commandParentName} {$this->getCommandParentName()} {$this->getCommandName()} --name='hero' --component='true'

				## RESOURCES

				All our blocks can be found here:
				https://github.com/infinum/eightshift-frontend-libs/tree/develop/blocks/init/src/Blocks/custom
			"),
		];
	}

	/**
	 * Copy a dummy folder, rename files and folders, and edit files content.
	 *
	 * ## OPTIONS
	 *
	 * <argument>
	 * : The argument to be used as the variable for renaming files and folders.
	 *
	 * ## EXAMPLES
	 *
	 * wp boilerplate blocks rename --name='test'
	 *
	 * wp boilerplate blocks rename --name='test' --component='true'
	 *
	 * @param array<string, mixed> $args Command arguments.
	 * @param array<string, mixed> $assocArgs $assocArgs Command associative arguments.
	 * @param string $destination Destination folder.
	 * 
	 * @return void
	 */
	public function renameBlock($args, $assocArgs, $destination): void
	{
		$blockName = $assocArgs['name'];

		// Specify the destinations directory.
		$destinationDir = $destination . 'dummy';
		$newDestinationDir = $destination . $blockName;

		// Rename files and folders in the destination directory.
		$this->renameFilesAndFolders($destinationDir, $blockName, $newDestinationDir);

		// Edit the contents of each file in the destination directory.
		$this->editFileContents($newDestinationDir, $blockName, $args);

		WP_CLI::success('Folder copied, renamed, and contents modified successfully.');
	}

	/**
	 * Rename files and folders in a directory.
	 *
	 * @param string $directory Directory to rename files and folders.
	 * @param string $blockName The variable for renaming files and folders.
	 * @param string $newDestinationDir Path to the new destination.
	 * 
	 * @return void
	 */
	private function renameFilesAndFolders($directory, $blockName, $newDestinationDir): void
	{
		$dir = new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS);
		$iterator = new RecursiveIteratorIterator($dir, RecursiveIteratorIterator::SELF_FIRST);

		foreach ($iterator as $file) {
			if ($file->isFile()) {
				$filePath = $file->getPathname();
				$fileName = $file->getFilename();
				$newFileName = \str_replace('dummy', $blockName, $fileName);

				if ($newFileName !== $fileName) {
					$newFilePath = $file->getPath() . \DIRECTORY_SEPARATOR . $newFileName;
					\rename($filePath, $newFilePath);
				}
			}
		}

		// Renames parent folder.
		\rename($directory, $newDestinationDir);
	}

	/**
	 * Edit the contents of each file in a directory.
	 *
	 * @param string $directory Directory to edit file contents.
	 * @param string $blockName The block name to be used as the variable for editing file contents.
	 * @param array<string, mixed> $args Array of arguments from WP-CLI command.
	 * 
	 * @return void
	 */
	private function editFileContents($directory, $blockName, $args): void
	{
		$dir = new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS);
		$iterator = new RecursiveIteratorIterator($dir, RecursiveIteratorIterator::SELF_FIRST);

		$namespace = $this->getNamespace($args);

		$kebabCaseString = $blockName;
		$pascalCaseString = \ucfirst(Components::kebabToCamelCase($kebabCaseString));
		$titleCaseString = \ucwords(\str_replace('-', ' ', $kebabCaseString));
		$camelCaseString = Components::kebabToCamelCase($kebabCaseString);

		foreach ($iterator as $file) {
			if ($file->isFile()) {
				$filePath = $file->getPathname();
				$fileContents = \file_get_contents($filePath);
				$fileExt = \pathinfo($filePath, \PATHINFO_EXTENSION);

				$newFileContents = \str_replace('%block-name-camel-case%', $camelCaseString, $fileContents);
				$newFileContents = \str_replace('%block-name-pascal-case%', $pascalCaseString, $newFileContents);
				$newFileContents = \str_replace('%block-name-title-case%', $titleCaseString, $newFileContents);
				$newFileContents = \str_replace('%block-name-kebab-case%', $kebabCaseString, $newFileContents);
				$newFileContents = \str_replace('%block-name-kebab-case%', $kebabCaseString, $newFileContents);

				if ($fileExt !== 'json') {
					$newFileContents = \str_replace('eightshift-frontend-libs', \lcfirst($namespace), $newFileContents);
				}

				if ($fileExt === 'php') {
					$newFileContents = \str_replace('EightshiftBoilerplate', $namespace, $newFileContents);
				}

				\file_put_contents($filePath, $newFileContents);
			}
		}
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		$this->getIntroText($assocArgs);

		$groupOutput = $assocArgs['groupOutput'] ?? false;

		$component = isset($assocArgs['component']) ? true : false;

		$source = $component ? Components::getProjectPaths('blocksSourceComponents') : Components::getProjectPaths('blocksSourceCustom');
		$destination = $component ? Components::getProjectPaths('blocksDestinationComponents') : Components::getProjectPaths('blocksDestinationCustom');

        if (is_dir($destination)) {
            $name = $assocArgs['name'];
            WP_CLI::error("Folder '$name' already exists in $destination");
        }

		$this->moveItems(
			\array_merge(
				$assocArgs,
				[
					'name' => 'dummy',
				],
			),
			$source,
			$destination,
			$component ? 'component' : 'block'
		);

		$this->renameBlock($args, $assocArgs, $destination);

		if (!$groupOutput) {
			WP_CLI::log('--------------------------------------------------');

			$this->cliLog('Please run `npm start` again to make sure everything works correctly.', "M");
		}
	}
}
