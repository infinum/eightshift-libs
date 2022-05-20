<?php

/**
 * Class that registers WPCLI commands used as parent placeholders.
 *
 * @package EightshiftLibs\Cli\ParentGroups
 */

declare(strict_types=1);

namespace EightshiftLibs\Cli\ParentGroups;

use WP_CLI_Command;

/**
 * Commands used copy block editor specific features to your project like blocks, components, wrapper, etc.
 */
class CliUse extends WP_CLI_Command
{
	/**
	 * Cli command name parent constant.
	 */
	public const COMMAND_NAME = 'use';
}
 