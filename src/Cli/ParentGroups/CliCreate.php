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
 * Service classes with specific functionality for your project.
 *
 * ## EXAMPLES
 *
 *    # Create media service class.
 *    $ wp boilerplate create media
 *
 *    # Create custom post type service class.
 *    $ wp boilerplate create post-type --url='project.test' --label="Jobs" --slug="jobs" --rewrite_url="jobs" --rest_endpoint_slug="jobs"
 *
 *    # Create custom taxonomy service class.
 *    $ wp boilerplate create taxonomy --label='Job Positions' --slug='job-position' --rest_endpoint_slug='job-positions' --post_type_slug='user'
 *
 *    # Create create custom admin appearance service class.
 *    $ wp boilerplate create modify-admin-appearance
 */
class CliCreate extends WP_CLI_Command
{
	/**
	 * Cli command name parent constant.
	 */
	public const COMMAND_NAME = 'create';
}
