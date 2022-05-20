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
 * Commands on media to generate, delete, use media WebP format in your project.
 */
class CliWebp extends WP_CLI_Command
{
	/**
	 * Cli command name parent constant.
	 */
	public const COMMAND_NAME = 'webp';
}
 