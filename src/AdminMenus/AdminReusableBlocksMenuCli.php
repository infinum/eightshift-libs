<?php

/**
 * Class that registers WPCLI command for Admin reusable blocks menu creation.
 *
 * @package EightshiftLibs\AdminMenus
 */

declare(strict_types=1);

namespace EightshiftLibs\AdminMenus;

use EightshiftBoilerplate\AdminMenus\AdminReusableBlocksMenuExample;
use EightshiftLibs\Cli\AbstractCli;

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
		return 'create';
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
	 * @return array<string, mixed>
	 */
	public function getDevelopArgs(array $args): array
	{
		return [
			'title' => $args[1] ?? 'Reusable Blocks',
			'menu_title' => $args[2] ?? 'Reusable Blocks',
			'capability' => $args[3] ?? 'edit_posts',
			'menu_icon' => $args[5] ?? 'dashicons-editor-table',
			'menu_position' => $args[6] ?? 4,
		];
	}

	/**
	 * Get WPCLI command doc.
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Generates reusable blocks admin menu class file.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'title',
					'description' => 'The text to be displayed in the title tags of the page when the menu is selected.',
					'optional' => true
				],
				[
					'type' => 'assoc',
					'name' => 'menu_title',
					'description' => 'The text to be used for the menu.',
					'optional' => true
				],
				[
					'type' => 'assoc',
					'name' => 'capability',
					'description' => 'The capability required for this menu to be displayed to the user.',
					'optional' => true
				],
				[
					'type' => 'assoc',
					'name' => 'menu_icon',
					'description' => 'The default menu icon for the admin menu. Example: dashicons-admin-generic.',
					'optional' => true,
				],
				[
					'type' => 'assoc',
					'name' => 'menu_position',
					'description' => 'The default menu position. Example: 20.',
					'optional' => true,
				],
			],
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		// Get Arguments.
		$title = $assocArgs['title'] ?? 'Reusable Blocks';
		$menuTitle = $assocArgs['menu_title'] ?? 'Reusable Blocks';
		$capability = $assocArgs['capability'] ?? AdminReusableBlocksMenuExample::ADMIN_REUSABLE_BLOCKS_MENU_CAPABILITY;
		$menuIcon = $assocArgs['menu_icon'] ?? AdminReusableBlocksMenuExample::ADMIN_REUSABLE_BLOCKS_MENU_ICON;
		$menuPosition = (string)($assocArgs['menu_position'] ?? AdminReusableBlocksMenuExample::ADMIN_REUSABLE_BLOCKS_MENU_POSITION);

		// Get full class name.
		$className = $this->getFileName('');
		$className = $className . $this->getClassShortName();

		// Read the template contents, and replace the placeholders with provided variables.
		$class = $this->getExampleTemplate(__DIR__, $this->getClassShortName())
			->renameClassNameWithPrefix($this->getClassShortName(), $className)
			->renameNamespace($assocArgs)
			->renameUse($assocArgs)
			->renameTextDomain($assocArgs)
			->searchReplaceString('Reusable Blocks', $title)
			->searchReplaceString('Reusable Blocks', $menuTitle)
			->searchReplaceString("'edit_posts'", "'{$capability}'");

		if (!empty($menuPosition)) {
			$class->searchReplaceString('100', $menuPosition);
		}

		if (!empty($menuIcon)) {
			$class->searchReplaceString('dashicons-admin-generic', $menuIcon);
		}

		// Output final class to new file/folder and finish.
		$class->outputWrite(static::OUTPUT_DIR, $className, $assocArgs);
	}
}
