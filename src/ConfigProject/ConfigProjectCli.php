<?php

/**
 * Class that registers WPCLI command for Config Project.
 *
 * @package EightshiftLibs\Config
 */

declare(strict_types=1);

namespace EightshiftLibs\ConfigProject;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliCreate;
use EightshiftLibs\Helpers\Helpers;
use WP_CLI;

/**
 * Class ConfigProjectCli
 */
class ConfigProjectCli extends AbstractCli
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
		return 'config-project';
	}

	/**
	 * Define default arguments.
	 *
	 * @return array<string, int|string|boolean>
	 */
	public function getDefaultArgs(): array
	{
		return [
			'path' => Helpers::getProjectPaths('projectRoot'),
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
					'name' => 'path',
					'description' => 'Define absolute path to project root folder.',
					'optional' => true,
					'default' => $this->getDefaultArg('path'),
				],
			],
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Used to extend project configuration and limit functionality depending on the environment like plugins update, file editing, upload, etc. This file will be copied to your project root folder.

				## EXAMPLES

				# Copy file:
				$ wp {$this->commandParentName} {$this->getCommandParentName()} {$this->getCommandName()}

				## RESOURCES

				File will be created from this example:
				https://github.com/infinum/eightshift-libs/blob/develop/src/ConfigProject/ConfigProjectExample.php
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		$this->getIntroText($assocArgs);

		// Get Props.
		$path = $this->getArg($assocArgs, 'path');

		// Read the template contents, and replace the placeholders with provided variables.
		$class = $this->getExampleTemplate(__DIR__, $this->getClassShortName())
			->renameGlobals($assocArgs);

		// Output final class to new file/folder and finish.
		$class->outputWrite($path, 'wp-config-project.php', $assocArgs);

		if (!\defined('WP_DEBUG')) {
			WP_CLI::runcommand('config delete WP_DEBUG');
		}

		WP_CLI::runcommand('config set WP_ENVIRONMENT_TYPE development');
		WP_CLI::runcommand("config set \"configProject\" \"require_once(ABSPATH . 'wp-config-project.php')\" --raw --type='variable'");
	}
}
