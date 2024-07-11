<?php

/**
 * Class that registers WPCLI command for patterns header/footer picker creation.
 *
 * @package EightshiftLibs\AdminMenus\AdminPatternsHeaderFooterMenu
 */

declare(strict_types=1);

namespace EightshiftLibs\AdminMenus\AdminPatternsHeaderFooterMenu;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliCreate;
use EightshiftLibs\Helpers\Helpers;

/**
 * Class AdminPatternsHeaderFooterMenuCli
 */
class AdminPatternsHeaderFooterMenuCli extends AbstractCli
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
		return 'patterns-header-footer';
	}

	/**
	 * Define default arguments.
	 *
	 * @return array<string, int|string|boolean>
	 */
	public function getDefaultArgs(): array
	{
		return [
			'title' => 'Header & footer',
			'menu_title' => 'Header & footer',
			'capability' => 'edit_posts',
			'menu_position' => 59,
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
			'shortdesc' => 'Create reusable blocks header/footer admin menu service class.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'title',
					'description' => 'The text to be displayed in the title tags of the page when the menu is selected.',
					'optional' => true,
					'default' => $this->getDefaultArg('title'),
				],
				[
					'type' => 'assoc',
					'name' => 'menu_title',
					'description' => 'The text to be used for the menu.',
					'optional' => true,
					'default' => $this->getDefaultArg('menu_title'),
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
					'name' => 'menu_position',
					'description' => 'The default menu position.',
					'optional' => true,
					'default' => $this->getDefaultArg('menu_position'),
				],
			],
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Used to initialize reusable block header/footer - adds pickers to the admin.

				## EXAMPLES

				$ wp {$this->commandParentName} {$this->getCommandParentName()} {$this->getCommandName()}

				## RESOURCES

				Service class will be created from this example:
				https://github.com/infinum/eightshift-libs/blob/develop/src/AdminMenus/AdminPatternsHeaderFooterMenu/PatternsHeaderFooterExample.php
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
		$menuPosition = (string)($this->getArg($assocArgs, 'menu_position'));
		$capability = $this->getArg($assocArgs, 'capability');

		// Get full class name.
		$className = $this->getFileName('');
		$className = $className . $this->getClassShortName();

		// Read the template contents, and replace the placeholders with provided variables.
		$class = $this->getExampleTemplate(__DIR__, $this->getClassShortName())
			->renameClassNameWithPrefix($this->getClassShortName(), $className)
			->renameGlobals($assocArgs)
			->searchReplaceString($this->getArgTemplate('title'), $title)
			->searchReplaceString($this->getArgTemplate('capability'), $capability)
			->searchReplaceString($this->getArgTemplate('menu_title'), $menuTitle);

		if (!empty($menuPosition)) {
			$class->searchReplaceString($this->getDefaultArg('menu_position'), $menuPosition);
		}

		// Output final class to new file/folder and finish.
		$class->outputWrite(Helpers::getProjectPaths('srcDestination', 'AdminMenus'), "{$className}.php", $assocArgs);
	}
}
