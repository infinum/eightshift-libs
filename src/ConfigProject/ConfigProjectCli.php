<?php

/**
 * Class that registers WPCLI command for Config Project.
 *
 * @package EightshiftLibs\Config
 */

declare(strict_types=1);

namespace EightshiftLibs\ConfigProject;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliProject;
use EightshiftLibs\Helpers\Components;
use WP_CLI;

/**
 * Class ConfigProjectCli
 */
class ConfigProjectCli extends AbstractCli
{
	/**
	 * Output dir relative path.
	 */
	public const OUTPUT_DIR = '..' . \DIRECTORY_SEPARATOR . '..' . \DIRECTORY_SEPARATOR . '..' . \DIRECTORY_SEPARATOR;

	/**
	 * Get WPCLI command parent name
	 *
	 * @return string
	 */
	public function getCommandParentName(): string
	{
		return CliProject::COMMAND_NAME;
	}

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'config_project';
	}

	/**
	 * Define default arguments.
	 *
	 * @return array<string, int|string|boolean>
	 */
	public function getDefaultArgs(): array
	{
		return [
			'root' => self::OUTPUT_DIR,
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
			'shortdesc' => 'Copy config file to control global variables used in the WordPress project.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'root',
					'description' => 'Define project root relative to initialization file of WP CLI.',
					'optional' => true,
					'default' => $this->getDefaultArg('root'),
				],
			],
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Used to extend project configuration and limit functionality depending on the environment like plugins update, file editing, upload, etc. This file will be copied to your project root folder.

				## EXAMPLES

				# Copy file:
				$ wp boilerplate {$this->getCommandParentName()} {$this->getCommandName()}

				## RESOURCES

				File will be created from this example:
				https://github.com/infinum/eightshift-libs/blob/develop/src/ConfigProject/ConfigProjectExample.php
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		// Get Props.
		$root = $this->getArg($assocArgs, 'root');

		// Read the template contents, and replace the placeholders with provided variables.
		$class = $this->getExampleTemplate(__DIR__, $this->getClassShortName())
			->renameUse($assocArgs)
			->renameTextDomain($assocArgs);

		// Output final class to new file/folder and finish.
		$class->outputWrite($root, 'wp-config-project.php', $assocArgs);

		WP_CLI::success("Please do the following steps manually to complete the setup:");
		WP_CLI::success("1. In wp-config.php - Make sure to define WP_ENVIRONMENT_TYPE const to 'development' like so: <?php define( 'WP_ENVIRONMENT_TYPE', 'development' ); ?>`");
		WP_CLI::success("2. In wp-config.php - Make sure to require wp-config-project.php (at the end of the file) but before the wp-settings.php. Like this:`);");
		WP_CLI::success("
		/** Absolute path to the WordPress directory. */
		if ( !\defined('ABSPATH') ) {
			define('ABSPATH', \dirname(__FILE__) . '/');
		}
		
		// Include wp config for your project.
		 require_once(ABSPATH . 'wp-config-project.php');
		
		/** Sets up WordPress vars and included files. */
		require_once(ABSPATH . 'wp-settings.php');
		");
	}
}
