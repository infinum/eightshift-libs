<?php

/**
 * WPCLI Helper methods.
 *
 * @package EightshiftLibs\Cli
 */

declare(strict_types=1);

namespace EightshiftLibs\Cli;

use EightshiftLibs\Helpers\Components;
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
	 * Output WP_CLI log with color.
	 *
	 * @param string $msg Msg to output.
	 * @param string $color Color to use from this list https://make.wordpress.org/cli/handbook/references/internal-api/wp-cli-colorize/.
	 *
	 * @return void
	 */
	protected function cliLog(string $msg, string $color = ''): void
	{
		if ($color === 'mixed') {
			WP_CLI::log(WP_CLI::colorize("{$msg}%n"));
			return;
		}

		if ($color) {
			WP_CLI::log(WP_CLI::colorize("%{$color}{$msg}%n"));
			return;
		}

		WP_CLI::log($msg);
	}

	/**
	 * Fancy WP_CLI log output in a box.
	 *
	 * @param string $msg Msg to output.
	 * @param string $type Type of message, either "success", "error", "warning" or "info".
	 *
	 * @return void
	 */
	protected function cliLogAlert(string $msg, string $type = 'success', $heading = ''): void
	{
		$colorToUse = '%G';
		$defaultHeading = __('Success!', 'eightshift-libs');

		switch ($type) {
			case 'warning':
				$colorToUse = '%Y';
				$defaultHeading = __('Warning', 'eightshift-libs');
				break;
			case 'info':
				$colorToUse = '%B';
				$defaultHeading = __('Info', 'eightshift-libs');
				break;
			case 'error':
				$colorToUse = '%R';
				$defaultHeading = __('Something went wrong', 'eightshift-libs');
				break;
		}

		$headingToUse = empty($heading) ? $defaultHeading : $heading;

		$output = "
		{$colorToUse}╭
		│ {$headingToUse}
		│ %n{$msg}{$colorToUse}
		╰
		";

		WP_CLI::log($output);
	}

	/**
	 * Return shorten CLI path output
	 *
	 * @param string $path Path to check.
	 * @param string $ref Ref from getProjectPaths to remove.
	 *
	 * @return string
	 */
	protected function getShortenCliPathOutput(string $path, string $ref = 'projectRoot'): string
	{
		return \str_replace(Components::getProjectPaths($ref), '', $path);
	}

	/**
	 * Scan folder for items.
	 *
	 * @param string $path Path to search.
	 *
	 * @return array<int, string>
	 */
	protected function getFolderItems(string $path): array
	{
		$output = \array_diff(\scandir($path), ['..', '.']);
		$output = \array_values($output);

		return $output;
	}
}
