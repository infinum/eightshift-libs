<?php

/**
 * Class that registers WP-CLI commands used as top level placeholders.
 *
 * @package EightshiftLibs\Cli\ParentGroups
 */

declare(strict_types=1);

namespace EightshiftLibs\Cli\ParentGroups;

use WP_CLI_Command;

/**
 * Eightshift project commands here to help you with your new project.
 *
 * ## NOTE
 *
 * We use boilerplate for all example commands, but this command prefix depends on your project setup.
 * This prefix can be changed in the `(new Cli())->load('boilerplate');` function call.
 * If you have changed the prefix to something else, please keep that in mind when reading the examples.
 *
 * ## RESOURCES
 *
 * Our documentation is located here:
 * https://infinum.github.io/eightshift-docs/
 */
class CliBoilerplate extends WP_CLI_Command
{
}
