<?php

/**
 * WPCLI Helper methods.
 *
 * @package EightshiftLibs\Cli
 */

declare(strict_types=1);

namespace EightshiftLibs\Cli;

use WP_CLI;
use WP_CLI\ExitException;

/**
 * CliHelpers trait
 */
trait CliHelpers
{
	/**
	 * WP CLI error logging helper
	 *
	 * A wrapper for the WP_CLI::error with error handling.
	 *
	 * @param string $errorMessage Error message to log in the CLI.
	 *
	 * @return void
	 */
	public static function cliError(string $errorMessage): void
	{
		try {
			WP_CLI::error($errorMessage);
			// @codeCoverageIgnoreStart
			// Cannot test the exit.
		} catch (ExitException $e) {
			exit("{$e->getCode()}: {$e->getMessage()}"); // phpcs:ignore Eightshift.Security.ComponentsEscape.OutputNotEscaped
			// @codeCoverageIgnoreEnd
		}
	}

	/**
	 * Returns the name of the github plugin without slashes. For example converts "infinum/eightshift-forms" to "eightshift-forms"
	 *
	 * @param string $name Name of the github package.
	 * @return string
	 */
	public static function getGithubPluginName(string $name): string
	{

		// If the plugin doesn't have a namespace, we're good, just return it.
		if (\strpos($name, '/') === false) {
			return $name;
		}

		$splitName = \explode('/', $name);

		return $splitName[\count($splitName) - 1];
	}
}
