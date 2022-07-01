<?php

/**
 * Class that registers WPCLI command for admin sub menu creation.
 *
 * @package EightshiftLibs\AdminMenus
 */

declare(strict_types=1);

namespace EightshiftLibs\AdminMenus;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliCreate;
use EightshiftLibs\Helpers\Components;

/**
 * Class AdminSubMenuCli
 */
class AdminSubMenuCli extends AbstractCli
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
		return 'admin_sub_menu';
	}

	/**
	 * Define default arguments.
	 *
	 * @return array<string, int|string|boolean>
	 */
	public function getDefaultArgs(): array
	{
		return [
			'parent_slug' => 'example-parent-slug',
			'title' => 'Admin Title',
			'menu_title' => 'Admin Sub Menu Title',
			'capability' => 'edit_posts',
			'menu_slug' => 'example-menu-slug',
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
			'shortdesc' => 'Create admin sub menu service class.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'parent_slug',
					'description' => 'The slug name for the parent menu (or the file name of a standard WordPress admin page)',
					'optional' => false,
				],
				[
					'type' => 'assoc',
					'name' => 'title',
					'description' => 'The text to be displayed in the title tags of the page when the menu is selected.',
					'optional' => false,
				],
				[
					'type' => 'assoc',
					'name' => 'menu_title',
					'description' => 'The text to be used for the menu.',
					'optional' => false,
				],
				[
					'type' => 'assoc',
					'name' => 'capability',
					'description' => 'The capability required for this menu to be displayed to the user.',
					'optional' => false,
				],
				[
					'type' => 'assoc',
					'name' => 'menu_slug',
					'description' => 'The slug name to refer to this menu by. Should be unique for this menu page and only include lowercase alphanumeric, dashes, and underscores characters to be compatible with sanitize_key().', // phpcs:ignore Generic.Files.LineLength.TooLong
					'optional' => false,
				],
			],
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Used to create child level admin pages for settings, works in combination with top level admin page.

				## EXAMPLES

				# Create service class:
				$ wp boilerplate {$this->getCommandParentName()} {$this->getCommandName()} --parent_slug='shop' --title='Content' --menu_title='content' --capability='edit_posts' --menu_slug='es-content'

				## RESOURCES

				Service class will be created from this example:
				https://github.com/infinum/eightshift-libs/blob/develop/src/AdminMenus/AdminSubMenuExample.php
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		// Get Arguments.
		$parentSlug = $this->getArg($assocArgs, 'parent_slug');
		$title = $this->getArg($assocArgs, 'title');
		$menuTitle = $this->getArg($assocArgs, 'menu_title');
		$capability = $this->getArg($assocArgs, 'capability');
		$menuSlug = $this->prepareSlug($this->getArg($assocArgs, 'menu_slug'));

		// Get full class name.
		$className = $this->getFileName($menuSlug);
		$className = $className . $this->getClassShortName();

		// Read the template contents, and replace the placeholders with provided variables.
		$this->getExampleTemplate(__DIR__, $this->getClassShortName())
			->renameClassNameWithPrefix($this->getClassShortName(), $className)
			->renameNamespace($assocArgs)
			->renameUse($assocArgs)
			->renameTextDomain($assocArgs)
			->searchReplaceString($this->getArgTemplate('parent_slug'), $parentSlug)
			->searchReplaceString($this->getArgTemplate('title'), $title)
			->searchReplaceString($this->getArgTemplate('menu_title'), $menuTitle)
			->searchReplaceString($this->getArgTemplate('capability'), $capability)
			->searchReplaceString($this->getArgTemplate('menu_slug'), $menuSlug)
			->outputWrite(Components::getProjectPaths('srcDestination', 'AdminMenus'), "{$className}.php", $assocArgs);
	}
}
