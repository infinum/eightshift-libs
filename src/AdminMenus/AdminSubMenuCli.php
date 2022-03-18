<?php

/**
 * Class that registers WPCLI command for admin sub menu creation.
 *
 * @package EightshiftLibs\AdminMenus
 */

declare(strict_types=1);

namespace EightshiftLibs\AdminMenus;

use EightshiftBoilerplate\AdminMenus\AdminSubMenuExample;
use EightshiftLibs\Cli\AbstractCli;

/**
 * Class AdminSubMenuCli
 */
class AdminSubMenuCli extends AbstractCli
{
	/**
	 * Output dir relative path.
	 *
	 * @var string
	 */
	public const OUTPUT_DIR = 'src' . \DIRECTORY_SEPARATOR . 'AdminMenus';

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
			'parent_slug' => $args[1] ?? 'example-menu-slug',
			'title' => $args[2] ?? 'Admin Title',
			'menu_title' => $args[3] ?? 'Admin Sub Menu Title',
			'capability' => $args[4] ?? 'edit_posts',
			'menu_slug' => $args[5] ?? 'admin_title',
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
			'shortdesc' => 'Generates admin sub menu class file.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'parent_slug',
					'description' => 'The slug name for the parent menu (or the file name of a standard WordPress admin page)',
					'optional' => \defined('ES_DEVELOP_MODE') ?? false
				],
				[
					'type' => 'assoc',
					'name' => 'title',
					'description' => 'The text to be displayed in the title tags of the page when the menu is selected.',
					'optional' => \defined('ES_DEVELOP_MODE') ?? false
				],
				[
					'type' => 'assoc',
					'name' => 'menu_title',
					'description' => 'The text to be used for the menu.',
					'optional' => \defined('ES_DEVELOP_MODE') ?? false
				],
				[
					'type' => 'assoc',
					'name' => 'capability',
					'description' => 'The capability required for this menu to be displayed to the user.',
					'optional' => \defined('ES_DEVELOP_MODE') ?? false
				],
				[
					'type' => 'assoc',
					'name' => 'menu_slug',
					'description' => 'The slug name to refer to this menu by. Should be unique for this menu page and only include lowercase alphanumeric, dashes, and underscores characters to be compatible with sanitize_key().', // phpcs:ignore Generic.Files.LineLength.TooLong
					'optional' => \defined('ES_DEVELOP_MODE') ?? false
				],
			],
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		// Get Arguments.
		$parentSlug = $assocArgs['parent_slug'] ?? AdminSubMenuExample::PARENT_MENU_SLUG;
		$title = $assocArgs['title'] ?? 'Admin Title';
		$menuTitle = $assocArgs['menu_title'] ?? 'Admin Sub Menu Title';
		$capability = $assocArgs['capability'] ?? AdminSubMenuExample::ADMIN_MENU_CAPABILITY;
		$menuSlug = $this->prepareSlug($assocArgs['menu_slug'] ?? AdminSubMenuExample::ADMIN_MENU_SLUG);

		// Get full class name.
		$className = $this->getFileName($menuSlug);
		$className = $className . $this->getClassShortName();

		// Read the template contents, and replace the placeholders with provided variables.
		$class = $this->getExampleTemplate(__DIR__, $this->getClassShortName())
			->renameClassNameWithPrefix($this->getClassShortName(), $className)
			->renameNamespace($assocArgs)
			->renameUse($assocArgs)
			->renameTextDomain($assocArgs)
			->searchReplaceString('example-parent-slug', $parentSlug)
			->searchReplaceString('Admin Title', $title)
			->searchReplaceString('Admin Sub Menu Title', $menuTitle)
			->searchReplaceString("'edit_posts'", "'{$capability}'")
			->searchReplaceString('example-menu-slug', $menuSlug);

		// Output final class to new file/folder and finish.
		$class->outputWrite(static::OUTPUT_DIR, $className, $assocArgs);
	}
}
