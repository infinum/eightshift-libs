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
 * Copy functionality from our library to your project like blocks, components, wrapper, etc.
 *
 * ## EXAMPLES
 *
 *    # Copy block by name.
 *    $ wp boilerplate blocks use block --name='paragraph'
 *
 *    # Copy component by name.
 *    $ wp boilerplate blocks use component --name='heading'
 *
 *    # Copy wrapper.
 *    $ wp boilerplate blocks use wrapper
 */
class CliBlocksUse extends WP_CLI_Command
{
	/**
	 * Cli command name parent constant.
	 */
	public const COMMAND_NAME = 'blocks use';
}
