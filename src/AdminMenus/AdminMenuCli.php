<?php

/**
 * Class that registers WPCLI command for Admin menu creation.
 *
 * @package EightshiftLibs\AdminMenus
 */

declare(strict_types=1);

namespace EightshiftLibs\AdminMenus;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliCreate;
use EightshiftLibs\Helpers\Helpers;

/**
 * Class AdminMenuCli
 */
class AdminMenuCli extends AbstractCli
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
		return 'admin-menu';
	}

	/**
	 * Define default arguments.
	 *
	 * @return array<string, int|string|boolean>
	 */
	public function getDefaultArgs(): array
	{
		return [
			'title' => 'Admin Title',
			'menu_title' => 'Admin Menu Title',
			'capability' => 'edit_posts',
			'menu_slug' => 'example-menu-slug',
			'menu_icon' => 'dashicons-admin-generic',
			'menu_position' => 100,
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
			'shortdesc' => 'Create admin menu service class.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'title',
					'description' => 'The text to be displayed in the title tags of the page when the menu is selected.',
					'optional' => false,
				],
				[
					'type' => 'assoc',
					'name' => 'menu_title',
					'description' => 'The text to be used for the sidebar menu.',
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
					'description' => 'The slug name to refer to this menu by. Should be unique for this menu page and only include lowercase alphanumeric, dashes and underscore characters to be compatible with sanitize_key().',
					'optional' => false,
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

				Used to create top level admin pages for settings and etc.

				## EXAMPLES

				# Create service class:
				$ wp {$this->commandParentName} {$this->getCommandParentName()} {$this->getCommandName()} --title='Content' --menu_title='content' --capability='edit_posts' --menu_slug='es-content'

				## RESOURCES

				Service class will be created from this example:
				https://github.com/infinum/eightshift-libs/blob/develop/src/AdminMenus/AdminMenuExample.php
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		$assocArgs = $this->prepareArgs($assocArgs);
		$this->getIntroText($assocArgs);

		// Get Arguments.
		$title = $this->getArg($assocArgs, 'title');
		$menuTitle = $this->getArg($assocArgs, 'menu_title');
		$capability = $this->getArg($assocArgs, 'capability');
		$menuSlug = $this->prepareSlug($this->getArg($assocArgs, 'menu_slug'));
		$menuIcon =  $this->getArg($assocArgs, 'menu_icon');
		$menuPosition = $this->getArg($assocArgs, 'menu_position');

		// Get full class name.
		$className = $this->getFileName($menuSlug);
		$className = $className . $this->getClassShortName();

		// Read the template contents, and replace the placeholders with provided variables.
		$class = $this->getExampleTemplate(__DIR__, $this->getClassShortName())
			->renameClassNameWithPrefix($this->getClassShortName(), $className)
			->renameGlobals($assocArgs)
			->searchReplaceString($this->getArgTemplate('title'), $title)
			->searchReplaceString($this->getArgTemplate('menu_title'), $menuTitle)
			->searchReplaceString($this->getArgTemplate('capability'), $capability)
			->searchReplaceString($this->getArgTemplate('menu_slug'), $menuSlug);

		if (!empty($menuPosition)) {
			$class->searchReplaceString($this->getDefaultArg('menu_position'), $menuPosition);
		}

		if (!empty($menuIcon)) {
			$class->searchReplaceString($this->getArgTemplate('menu_icon'), $menuIcon);
		}

		// Output final class to new file/folder and finish.
		$class->outputWrite(Helpers::getProjectPaths('srcDestination', 'AdminMenus'), "{$className}.php", $assocArgs);
	}
}
