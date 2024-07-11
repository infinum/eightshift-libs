<?php

/**
 * The class file that holds abstract class for WPCLI
 *
 * @package EightshiftLibs\Cli
 */

declare(strict_types=1);

namespace EightshiftLibs\Cli;

use EightshiftLibs\AdminMenus\AdminMenuCli;
use EightshiftLibs\AdminMenus\AdminPatternsHeaderFooterMenu\AdminPatternsHeaderFooterMenuCli;
use EightshiftLibs\AdminMenus\AdminPatternsMenu\AdminPatternsMenuCli;
use EightshiftLibs\AdminMenus\AdminSubMenuCli;
use EightshiftLibs\AdminMenus\AdminThemeOptionsMenu\AdminThemeOptionsMenuCli;
use EightshiftLibs\AnalyticsGdpr\AnalyticsGdprCli;
use EightshiftLibs\BlockPatterns\BlockPatternCli;
use EightshiftLibs\Blocks\BlocksCli;
use EightshiftLibs\Blocks\UseComponentCli;
use EightshiftLibs\Blocks\UseBlockCli;
use EightshiftLibs\Blocks\UseAssetsCli;
use EightshiftLibs\Blocks\UseGlobalAssetsCli;
use EightshiftLibs\Blocks\UseManifestCli;
use EightshiftLibs\Blocks\UseVariationCli;
use EightshiftLibs\Blocks\UseWrapperCli;
use EightshiftLibs\Cache\ManifestCacheCli;
use EightshiftLibs\Cli\ParentGroups\CliBoilerplate;
use EightshiftLibs\Cli\ParentGroups\CliCreate;
use EightshiftLibs\Cli\ParentGroups\CliRun;
use EightshiftLibs\Cli\ParentGroups\CliBlocks;
use EightshiftLibs\Cli\ParentGroups\CliInit;
use EightshiftLibs\Cli\ParentGroups\CliInitSetup;
use EightshiftLibs\Columns\Media\WebPMediaColumnCli;
use EightshiftLibs\Config\ConfigThemeCli;
use EightshiftLibs\Config\ConfigPluginCli;
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
use EightshiftLibs\Media\MediaCli;
use EightshiftLibs\Menu\MenuCli;
use EightshiftLibs\ModifyAdminAppearance\ModifyAdminAppearanceCli;
use EightshiftLibs\Rest\Fields\FieldCli;
use EightshiftLibs\Rest\Routes\RouteCli;
use EightshiftLibs\Db\ExportCli;
use EightshiftLibs\Db\ImportCli;
use EightshiftLibs\Geolocation\GeolocationCli;
use EightshiftLibs\GitIgnore\GitIgnoreCli;
use EightshiftLibs\Init\InitAllCli;
use EightshiftLibs\Init\InitBlocksCli;
use EightshiftLibs\Init\InitPluginCleanCli;
use EightshiftLibs\Init\InitPluginCli;
use EightshiftLibs\Init\InitProjectCli;
use EightshiftLibs\Init\InitThemeCleanCli;
use EightshiftLibs\Init\InitThemeCli;
use EightshiftLibs\InitSetup\InitPluginCleanSetupCli;
use EightshiftLibs\InitSetup\InitPluginSetupCli;
use EightshiftLibs\InitSetup\InitThemeCleanSetupCli;
use EightshiftLibs\InitSetup\InitThemeSetupCli;
use EightshiftLibs\Media\RegenerateWebPMediaCli;
use EightshiftLibs\Media\UseWebPMediaCli;
use EightshiftLibs\Misc\VersionCli;
use EightshiftLibs\Optimization\OptimizationCli;
use EightshiftLibs\Plugin\PluginCli;
use EightshiftLibs\Readme\ReadmeCli;
use EightshiftLibs\Rest\Routes\LoadMore\LoadMoreRouteCli;
use EightshiftLibs\ThemeOptions\AcfThemeOptionsCli;
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
	 * All commands defined as parent list commands.
	 *
	 * @var array<string>
	 */
	public const PARENT_COMMANDS = [
		CliCreate::class,
		CliRun::class,
		CliBlocks::class,
		CliInit::class,
		CliInitSetup::class,
	];

	/**
	 * All commands that are service classes type. Command prefix - create.
	 *
	 * @var array<string>
	 */
	public const CREATE_COMMANDS = [
		AdminMenuCli::class,
		AdminPatternsMenuCli::class,
		AdminThemeOptionsMenuCli::class,
		AdminSubMenuCli::class,
		AdminPatternsHeaderFooterMenuCli::class,
		AnalyticsGdprCli::class,
		WebPMediaColumnCli::class,
		ConfigPluginCli::class,
		ConfigThemeCli::class,
		ConfigProjectCli::class,
		AcfMetaCli::class,
		PostTypeCli::class,
		TaxonomyCli::class,
		EnqueueAdminCli::class,
		EnqueueBlocksCli::class,
		EnqueueThemeCli::class,
		GeolocationCli::class,
		GitIgnoreCli::class,
		I18nCli::class,
		LoginCli::class,
		MainCli::class,
		MediaCli::class,
		MenuCli::class,
		ModifyAdminAppearanceCli::class,
		OptimizationCli::class,
		ReadmeCli::class,
		FieldCli::class,
		RouteCli::class,
		LoadMoreRouteCli::class,
		ServiceExampleCli::class,
		SetupCli::class,
		ThemeOptionsCli::class,
		AcfThemeOptionsCli::class,
		EscapedViewCli::class,
		WpCli::class,
		ManifestCacheCli::class,
		PluginCli::class,
	];

	/**
	 * All commands that can be used on a WP project directly from the libs. Command prefix - run.
	 *
	 * @var array<string>
	 */
	public const RUN_COMMANDS = [
		ExportCli::class,
		ImportCli::class,
		RegenerateWebPMediaCli::class,
		UseWebPMediaCli::class,
		VersionCli::class,
	];

	/**
	 * All commands used for block editor. Command prefix - blocks.
	 *
	 * @var array<string>
	 */
	public const BLOCKS_COMMANDS = [
		BlockPatternCli::class,
		BlocksCli::class,
		UseAssetsCli::class,
		UseBlockCli::class,
		UseComponentCli::class,
		UseGlobalAssetsCli::class,
		UseManifestCli::class,
		UseVariationCli::class,
		UseWrapperCli::class,
	];

	/**
	 * All commands used for setting up. Command prefix - init.
	 *
	 * @var array<string>
	 */
	public const INIT_COMMANDS = [
		InitAllCli::class,
		InitBlocksCli::class,
		InitPluginCli::class,
		InitPluginCleanCli::class,
		InitProjectCli::class,
		InitThemeCli::class,
		InitThemeCleanCli::class,
	];

	/**
	 * All commands used for setting up. Command prefix - init-setup.
	 *
	 * @var array<string>
	 */
	public const INIT_SETUP_COMMANDS = [
		InitThemeSetupCli::class,
		InitThemeCleanSetupCli::class,
		InitPluginSetupCli::class,
		InitPluginCleanSetupCli::class,
	];

	/**
	 * Define all classes to register for normal WP.
	 *
	 * @return class-string[]
	 */
	public function getCommandsClasses(): array
	{
		return [
			...static::CREATE_COMMANDS,
			...static::BLOCKS_COMMANDS,
			...static::INIT_COMMANDS,
			...static::INIT_SETUP_COMMANDS,
			...static::RUN_COMMANDS,
		];
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
		// Duplicate condition because WP_CLI will throw error on the project.
		if (!\getenv('ES_TEST') && \defined('WP_CLI')) {
			// Top Level command name.
			WP_CLI::add_command($commandParentName, new CliBoilerplate());

			// Register all top level commands.
			foreach (self::PARENT_COMMANDS as $item) {
				$reflectionClass = new ReflectionClass($item);
				$class = $reflectionClass->newInstanceArgs();
				$name = $reflectionClass->getConstant('COMMAND_NAME');

				WP_CLI::add_command("{$commandParentName} {$name}", $class);
			}
		}

		foreach ($this->getCommandsClasses() as $item) {
			$reflectionClass = new ReflectionClass($item);
			$class = $reflectionClass->newInstanceArgs([$commandParentName]);

			if ($class instanceof CliInterface) {
				$class->register();
			}
		}
	}
}
