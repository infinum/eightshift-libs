<?php

/**
 * The class file that holds abstract class for WPCLI
 *
 * @package EightshiftLibs\Cli
 */

declare(strict_types=1);

namespace EightshiftLibs\Cli;

use EightshiftLibs\AdminMenus\AdminMenuCli;
use EightshiftLibs\AdminMenus\AdminReusableBlocksMenuCli;
use EightshiftLibs\AdminMenus\AdminSubMenuCli;
use EightshiftLibs\AnalyticsGdpr\AnalyticsGdprCli;
use EightshiftLibs\BlockPatterns\BlockPatternCli;
use EightshiftLibs\Blocks\BlocksCli;
use EightshiftLibs\Blocks\BlockComponentCli;
use EightshiftLibs\Blocks\BlockCli;
use EightshiftLibs\Blocks\BlocksStorybookCli;
use EightshiftLibs\Blocks\BlockVariationCli;
use EightshiftLibs\Blocks\BlockWrapperCli;
use EightshiftLibs\Build\BuildCli;
use EightshiftLibs\CiExclude\CiExcludeCli;
use EightshiftLibs\Cli\ParentGroups\CliBoilerplate;
use EightshiftLibs\Cli\ParentGroups\CliCreate;
use EightshiftLibs\Cli\ParentGroups\CliProject;
use EightshiftLibs\Cli\ParentGroups\CliRun;
use EightshiftLibs\Cli\ParentGroups\CliSetup;
use EightshiftLibs\Cli\ParentGroups\CliBlocks;
use EightshiftLibs\Cli\ParentGroups\CliWebp;
use EightshiftLibs\Columns\Media\WebPMediaColumnCli;
use EightshiftLibs\Config\ConfigCli;
use EightshiftLibs\ConfigProject\ConfigProjectCli;
use EightshiftLibs\View\EscapedViewCli;
use EightshiftLibs\CustomMeta\AcfMetaCli;
use EightshiftLibs\Setup\SetupCli;
use EightshiftLibs\CustomPostType\PostTypeCli;
use EightshiftLibs\CustomTaxonomy\TaxonomyCli;
use EightshiftLibs\Enqueue\Admin\EnqueueAdminCli;
use EightshiftLibs\Enqueue\Blocks\EnqueueBlocksCli;
use EightshiftLibs\Enqueue\Theme\EnqueueThemeCli;
use EightshiftLibs\Services\ServiceExampleCli;
use EightshiftLibs\I18n\I18nCli;
use EightshiftLibs\Login\LoginCli;
use EightshiftLibs\Main\MainCli;
use EightshiftLibs\Manifest\ManifestCli;
use EightshiftLibs\Media\MediaCli;
use EightshiftLibs\Menu\MenuCli;
use EightshiftLibs\ModifyAdminAppearance\ModifyAdminAppearanceCli;
use EightshiftLibs\Rest\Fields\FieldCli;
use EightshiftLibs\Rest\Routes\RouteCli;
use EightshiftLibs\Db\ExportCli;
use EightshiftLibs\Db\ImportCli;
use EightshiftLibs\Geolocation\GeolocationCli;
use EightshiftLibs\GitIgnore\GitIgnoreCli;
use EightshiftLibs\Media\RegenerateWebPMediaCli;
use EightshiftLibs\Media\UseWebPMediaCli;
use EightshiftLibs\Readme\ReadmeCli;
use EightshiftLibs\Setup\UpdateCli;
use EightshiftLibs\ThemeOptions\ThemeOptionsCli;
use EightshiftLibs\WpCli\WpCli;
use ReflectionClass;
// phpcs:ignore SlevomatCodingStandard.Namespaces.UnusedUses.UnusedUse
use Exception;
use WP_CLI;

/**
 * Class Cli
 */
class Cli
{
	/**
	 * All classes defined as parent list commands.
	 *
	 * @var class-string[]
	 */
	public const PARENTS_LIST = [
		CliCreate::class,
		CliProject::class,
		CliRun::class,
		CliSetup::class,
		CliBlocks::class,
		CliWebp::class,
	];

	/**
	 * All classes and commands that can be used on development and public WP CLI.
	 *
	 * @var class-string[]
	 */
	public const CLASSES_LIST = [
		AdminMenuCli::class,
		AdminReusableBlocksMenuCli::class,
		AdminSubMenuCli::class,
		AcfMetaCli::class,
		AnalyticsGdprCli::class,
		BlocksCli::class,
		EnqueueAdminCli::class,
		EnqueueBlocksCli::class,
		EnqueueThemeCli::class,
		EscapedViewCli::class,
		ConfigCli::class,
		PostTypeCli::class,
		TaxonomyCli::class,
		I18nCli::class,
		LoginCli::class,
		MainCli::class,
		ManifestCli::class,
		MediaCli::class,
		MenuCli::class,
		ModifyAdminAppearanceCli::class,
		FieldCli::class,
		RouteCli::class,
		ServiceExampleCli::class,
		ThemeOptionsCli::class,
		BuildCli::class,
		CiExcludeCli::class,
		ConfigProjectCli::class,
		GitIgnoreCli::class,
		WebPMediaColumnCli::class,
		ReadmeCli::class,
		SetupCli::class,
		WpCli::class,
		GeolocationCli::class,
	];

	/**
	 * All classes and commands that can be used on WP project.
	 *
	 * @var class-string[]
	 */
	public const COMMANDS_LIST = [
		RegenerateWebPMediaCli::class,
		UseWebPMediaCli::class,
	];

	/**
	 * All classes and commands used only for WPCLI - blocks.
	 *
	 * @var class-string[]
	 */
	public const BLOCKS_CLASSES = [
		BlockCli::class,
		BlockComponentCli::class,
		BlockVariationCli::class,
		BlockWrapperCli::class,
		BlockPatternCli::class,
		BlocksStorybookCli::class,
	];

	/**
	 * All classes and commands used only for WPCLI - project.
	 *
	 * @var class-string[]
	 */
	public const PROJECT_CLASSES = [
		ExportCli::class,
		ImportCli::class,
		UpdateCli::class,
	];

	/**
	 * All classes and commands used for project setup.
	 *
	 * @var class-string[]
	 */
	public const SETUP_CLASSES = [
		CliInitTheme::class,
		CliInitProject::class,
		CliInitAll::class,
	];

	/**
	 * All classes and commands used only for development.
	 *
	 * @var class-string[]
	 */
	public const DEVELOP_CLASSES = [
		CliReset::class,
		CliRunAll::class,
		CliShowAll::class,
	];

	/**
	 * Define all classes to register for development.
	 *
	 * @return class-string[]
	 */
	public function getDevelopClasses(): array
	{
		return \array_merge(
			static::CLASSES_LIST,
			static::DEVELOP_CLASSES,
			static::SETUP_CLASSES,
			static::COMMANDS_LIST
		);
	}

	/**
	 * Define all classes to register for normal WP.
	 *
	 * @return class-string[]
	 */
	public function getPublicClasses(): array
	{
		return \array_merge(
			static::CLASSES_LIST,
			static::BLOCKS_CLASSES,
			static::PROJECT_CLASSES,
			static::SETUP_CLASSES,
			static::COMMANDS_LIST
		);
	}

	/**
	 * Run all CLI commands for develop.
	 *
	 * @param string[] $args WPCLI eval-file arguments.
	 *
	 * @throws Exception Exception if the class doesn't exist.
	 *
	 * @return void
	 */
	public function loadDevelop(array $args = []): void
	{
		$commandName = $args[0] ?? '';

		if (empty($commandName)) {
			CliHelpers::cliError('First argument must be a valid command name.');
		}

		foreach ($this->getDevelopClasses() as $item) {
			$reflectionClass = new ReflectionClass($item);
			$class = $reflectionClass->newInstanceArgs(['null']);

			if (
				\method_exists($class, 'getCommandName') &&
				\method_exists($class, 'getCommandParentName') &&
				\method_exists($class, 'getDevelopArgs') &&
				\method_exists($class, '__invoke')
			) {
				if ("{$class->getCommandParentName()}_{$class->getCommandName()}" === $commandName) {
					$class->__invoke(
						[],
						$class->getDevelopArgs($args)
					);

					break;
				}
			}
		}
	}

	/**
	 * Run all CLI commands for normal WPCLI.
	 *
	 * @param string $commandParentName Define top level commands name.
	 *
	 * @throws Exception Exception if the class doesn't exist.
	 *
	 * @return void
	 */
	public function load(string $commandParentName): void
	{
		if (!\getenv('ES_TEST') && defined('WP_CLI')) {
			// Top Level command name.
			WP_CLI::add_command($commandParentName, new CliBoilerplate());

			// Register all top level commands.
			foreach (self::PARENTS_LIST as $item) {
				$reflectionClass = new ReflectionClass($item);
				$class = $reflectionClass->newInstanceArgs();
				$name = $reflectionClass->getConstant('COMMAND_NAME');

				WP_CLI::add_command("{$commandParentName} {$name}", $class);
			}
		}

		foreach ($this->getPublicClasses() as $item) {
			$reflectionClass = new ReflectionClass($item);
			$class = $reflectionClass->newInstanceArgs([$commandParentName]);

			if ($class instanceof CliInterface) {
				$class->register();
			}
		}
	}
}
