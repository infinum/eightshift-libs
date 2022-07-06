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
 * Create service class functionality from block editor like blocks and patterns.
 *
 * ## EXAMPLES
 *
 *    # Create blocks service class.
 *    $ wp boilerplate blocks create blocks_class
 *
 *    # Create blocks pattern service class.
 *    $ wp boilerplate blocks create block_pattern
 */
class CliBlocksCreate extends WP_CLI_Command
{
	/**
	 * Cli command name parent constant.
	 */
	public const COMMAND_NAME = 'blocks create';
}
