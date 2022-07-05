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
 * Block editor specific features for your project like blocks, components, wrapper, etc.
 *
 * ## EXAMPLES
 *
 *    # Copy block by name.
 *    $ wp boilerplate blocks use_block --name='paragraph'
 *
 *    # Copy component by name.
 *    $ wp boilerplate blocks use_component --name='heading'
 *
 *    # Copy wrapper.
 *    $ wp boilerplate blocks use_wrapper
 */
class CliBlocks extends WP_CLI_Command
{
	/**
	 * Cli command name parent constant.
	 */
	public const COMMAND_NAME = 'blocks';
}
