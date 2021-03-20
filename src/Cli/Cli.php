<?php

/**
 * The class file that holds abstract class for WPCLI
 *
 * @package EightshiftLibs\Cli
 */

declare(strict_types=1);

namespace EightshiftLibs\Cli;

use EightshiftLibs\Blocks\{BlocksCli, BlockComponentCli, BlockCli, BlocksStorybookCli, BlockVariationCli, BlockWrapperCli};
use EightshiftLibs\Build\BuildCli;
use EightshiftLibs\CiExclude\CiExcludeCli;
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
use EightshiftLibs\Db\{ExportCli, ImportCli};
use EightshiftLibs\GitIgnore\GitIgnoreCli;
use EightshiftLibs\Readme\ReadmeCli;
use EightshiftLibs\Setup\UpdateCli;
use EightshiftLibs\ThemeOptions\ThemeOptionsCli;

/**
 * Class Cli
 */
class Cli
{
	/**
	 * All classes and commands that can be used on development and public WP CLI.
	 *
	 * @var array
	 */
	public const CLASSES_LIST = [
		BlocksCli::class,
		EnqueueAdminCli::class,
		EnqueueBlocksCli::class,
		EnqueueThemeCli::class,
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
		BuildCli::class,
		ReadmeCli::class,
		GitIgnoreCli::class,
		CiExcludeCli::class,
		SetupCli::class,
		AcfMetaCli::class,
		EscapedViewCli::class,
		ThemeOptionsCli::class,
		ConfigProjectCli::class,
	];

	/**
	 * All classes and commands used only for WPCLI.
	 *
	 * @var array
	 */
	public const PUBLIC_CLASSES = [
		BlockComponentCli::class,
		BlockWrapperCli::class,
		BlockVariationCli::class,
		BlockCli::class,
		BlocksStorybookCli::class,
		UpdateCli::class,
		ExportCli::class,
		ImportCli::class,
	];

	/**
	 * All classes and commands used for project setup.
	 *
	 * @var array
	 */
	public const SETUP_CLASSES = [
		CliInitTheme::class,
		CliInitProject::class,
	];

	/**
	 * All classes and commands used only for development.
	 *
	 * @var array
	 */
	public const DEVELOP_CLASSES = [
		CliReset::class,
		CliRunAll::class,
		CliShowAll::class,
	];

	/**
	 * Define all classes to register for development.
	 *
	 * @return array
	 */
	public function getDevelopClasses(): array
	{
		return array_merge(
			static::CLASSES_LIST,
			static::DEVELOP_CLASSES,
			static::SETUP_CLASSES
		);
	}

	/**
	 * Define all classes to register for normal WP.
	 *
	 * @return array
	 */
	public function getPublicClasses(): array
	{
		return array_merge(
			static::CLASSES_LIST,
			static::PUBLIC_CLASSES,
			static::SETUP_CLASSES
		);
	}

	/**
	 * Run all CLI commands for develop.
	 *
	 * @param array $args WPCLI eval-file arguments.
	 *
	 * @throws \ReflectionException Exception if the class doesn't exist.
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
			$reflectionClass = new \ReflectionClass($item);
			$class = $reflectionClass->newInstanceArgs(['null']);

			if (method_exists($class, 'getCommandName') && method_exists($class, 'getDevelopArgs') && method_exists($class, '__invoke')) {
				if ($class->getCommandName() === $commandName) {
					$class->__invoke(
						[],
						$class->getDevelopArgs($args)
					);

					break;
				}
			}
		}
	}
}
