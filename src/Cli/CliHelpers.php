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
	 * Convert camel case to kebab case.
	 *
	 * @param string $input Input to convert.
	 * @return string
	 */
	public static function camelCaseToKebabCase(string $input): string
	{
		$output = \ltrim(\strtolower((string)\preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '-$0', $input)), '-');
		$output = \str_replace(['_', ' '], '-', $output);
		return \str_replace('--', '-', $output);
	}

	/**
	 * Convert string from kebab to camel case
	 *
	 * @param string $string    String to convert.
	 * @param string $separator Separator to use for conversion.
	 *
	 * @return string
	 */
	public static function kebabToCamelCase(string $string, string $separator = '-'): string
	{
		return \lcfirst(\str_replace($separator, '', \ucwords(mb_\strtolower($string), $separator)));
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
