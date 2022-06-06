<?php

/**
 * Class that registers WPCLI command for Admin reusable blocks menu creation.
 *
 * @package EightshiftLibs\AdminMenus
 */

declare(strict_types=1);

namespace EightshiftLibs\AdminMenus;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliCreate;

/**
 * Class AdminReusableBlocksMenuCli
 */
class AdminReusableBlocksMenuCli extends AbstractCli
{
	/**
	 * Output dir relative path.
	 *
	 * @var string
	 */
	public const OUTPUT_DIR = 'src' . \DIRECTORY_SEPARATOR . 'AdminMenus';

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
		return 'admin_reusable_blocks_menu';
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
			'title' => 'Test Reusable Blocks',
			'menu_title' => 'Test Reusable Blocks',
			'capability' => 'test_edit_posts',
			'menu_icon' => 'dashicons-editor-generic',
			'menu_position' => 100,
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
			'title' => 'Reusable Blocks',
			'menu_title' => 'Reusable Blocks',
			'capability' => 'edit_posts',
			'menu_icon' => 'dashicons-admin-table',
			'menu_position' => 4,
		];
	}

	/**
	 * Get WPCLI command doc.
	 *
	 * @return array<string, mixed>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Create reusable blocks admin menu service class.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'title',
					'description' => 'The text to be displayed in the title tags of the page when the menu is selected.',
					'optional' => true,
					'default' => 'Reusable Blocks',
				],
				[
					'type' => 'assoc',
					'name' => 'menu_title',
					'description' => 'The text to be used for the menu.',
					'optional' => true,
					'default' => 'Reusable Blocks',
				],
				[
					'type' => 'assoc',
					'name' => 'capability',
					'description' => 'The capability required for this menu to be displayed to the user.',
					'optional' => true,
					'default' => $this->getDefaultArg('capability'),
				],
				[
					'type' => 'assoc',
					'name' => 'menu_icon',
					'description' => 'The default menu icon for the admin menu.',
					'optional' => true,
					'default' => $this->getDefaultArg('menu_icon'),
				],
				[
					'type' => 'assoc',
					'name' => 'menu_position',
					'description' => 'The default menu position.',
					'optional' => true,
					'default' => $this->getDefaultArg('menu_position'),
				],
			],
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Used to create top level sidebar menu page for reusable blocks for easy usage.

				## EXAMPLES

				# Create service class:
				$ wp boilerplate {$this->getCommandParentName()} {$this->getCommandName()}

				## RESOURCES

				Service class will be created from this example:
				https://github.com/infinum/eightshift-libs/blob/develop/src/AdminMenus/AdminReusableBlocksMenuExample.php
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		// Get Arguments.
		$title = $this->getArg($assocArgs, 'title');
		$menuTitle = $this->getArg($assocArgs, 'menu_title');
		$capability = $this->getArg($assocArgs, 'capability');
		$menuIcon = $this->getArg($assocArgs, 'menu_icon');
		$menuPosition = (string)($this->getArg($assocArgs, 'menu_position'));

		// Get full class name.
		$className = $this->getFileName('');
		$className = $className . $this->getClassShortName();

		// Read the template contents, and replace the placeholders with provided variables.
		$class = $this->getExampleTemplate(__DIR__, $this->getClassShortName())
			->renameClassNameWithPrefix($this->getClassShortName(), $className)
			->renameNamespace($assocArgs)
			->renameUse($assocArgs)
			->renameTextDomain($assocArgs)
			->searchReplaceString($this->getArgTemplate('title'), $title)
			->searchReplaceString($this->getArgTemplate('menu_title'), $menuTitle)
			->searchReplaceString($this->getArgTemplate('capability'), $capability);

		if (!empty($menuPosition)) {
			$class->searchReplaceString($this->getDefaultArg('menu_position'), $menuPosition);
		}

		if (!empty($menuIcon)) {
			$class->searchReplaceString($this->getArgTemplate('menu_icon'), $menuIcon);
		}

		// Output final class to new file/folder and finish.
		$class->outputWrite(static::OUTPUT_DIR, $className, $assocArgs);
	}
}
