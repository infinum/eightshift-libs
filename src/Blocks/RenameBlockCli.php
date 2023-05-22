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
		return 'rename-block';
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
			],
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Used to copy and rename dummy block from our library to your project. After copying script will rename folders, files and their content with provided block name.

				## EXAMPLES

				# Rename example block by name:
				$ wp {$this->commandParentName} {$this->getCommandParentName()} {$this->getCommandName()} --name='hero'

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
     * wp boilerplate blocks rename-block --name='test'
     *
     * @param array $args Command arguments.
     * @param array $assocArgs Command associative arguments.
     */
    public function renameBlock($args, $assocArgs)
    {
        $blockName = $assocArgs['name'];

        // Specify the destinations directory.
        $destinationDir = Components::getProjectPaths('blocksDestinationCustom') . 'dummy';
        $newDestinationDir = Components::getProjectPaths('blocksDestinationCustom') . $blockName;

        // Rename files and folders in the destination directory.
        $this->rename_files_folders($destinationDir, $blockName);

        // Edit the contents of each file in the destination directory.
        $this->editFileContents($newDestinationDir, $blockName, $args);

        \WP_CLI::success('Folder copied, renamed, and contents modified successfully.');
        
    }

    /**
     * Rename files and folders in a directory.
     *
     * @param string $directory Directory to rename files and folders.
     * @param string $argument The argument to be used as the variable for renaming files and folders.
     */
    private function rename_files_folders($directory, $argument) 
    {
        $dir = new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS);
        $iterator = new \RecursiveIteratorIterator($dir, \RecursiveIteratorIterator::SELF_FIRST);
        
        $newDestinationDirectory = Components::getProjectPaths('blocksDestinationCustom') . $argument;

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $filePath = $file->getPathname();
                $fileName = $file->getFilename();
                $newFileName = str_replace('dummy', $argument, $fileName);

                if ($newFileName !== $fileName) {
                    $newFilePath = $file->getPath() . DIRECTORY_SEPARATOR . $newFileName;
                    rename($filePath, $newFilePath);
                }
            }
        }

        // Renames parent folder
        rename($directory, $newDestinationDirectory);
    }

    /**
     * Edit the contents of each file in a directory.
     *
     * @param string $directory Directory to edit file contents.
     * @param string $blockName The block name to be used as the variable for editing file contents.
     * @param array<string, mixed> $args Array of arguments from WP-CLI command.
     */
    private function editFileContents($directory, $blockName, $args)
    {
        $dir = new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS);
        $iterator = new \RecursiveIteratorIterator($dir, \RecursiveIteratorIterator::SELF_FIRST);

        $namespace = $this->getNamespace($args);

        $kebabCaseString = $blockName;
        $pascalCaseString = \ucfirst(Components::kebabToCamelCase($kebabCaseString));
        $titleCaseString = ucwords(str_replace('-', ' ', $kebabCaseString));
        $camelCaseString = Components::kebabToCamelCase($kebabCaseString);

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $filePath = $file->getPathname();
                $fileContents = file_get_contents($filePath);
                $fileExt = pathinfo($filePath, PATHINFO_EXTENSION);
                
                $newFileContents = str_replace('%block-name-camel-case%', $camelCaseString, $fileContents);
                $newFileContents = str_replace('%block-name-pascal-case%', $pascalCaseString, $newFileContents);
                $newFileContents = str_replace('%block-name-title-case%', $titleCaseString, $newFileContents);
                $newFileContents = str_replace('%block-name-kebab-case%', $kebabCaseString, $newFileContents);
                $newFileContents = str_replace('%block-name-kebab-case%', $kebabCaseString, $newFileContents);
                $newFileContents = str_replace('eightshift-frontend-libs', lcfirst($namespace), $newFileContents);

                if ($fileExt == 'php') {
                    $newFileContents = str_replace('EightshiftBoilerplate', $namespace, $newFileContents);
                }

                file_put_contents( $filePath, $newFileContents );
            }
        }
    }

    public function __invoke(array $args, array $assocArgs)
	{
		$this->getIntroText($assocArgs);

		$groupOutput = $assocArgs['groupOutput'] ?? false;

        $this->moveItems(
			\array_merge(
				$assocArgs,
				[
					'name' => 'dummy',
				],
			),
			Components::getProjectPaths('blocksSourceCustom'),
			Components::getProjectPaths('blocksDestinationCustom'),
			'block'
		);

        $this->renameBlock($args, $assocArgs);

        if (!$groupOutput) {
			WP_CLI::log('--------------------------------------------------');

			$this->cliLog('Please run `npm start` again to make sure everything works correctly.', "M");
		}
	}
}
