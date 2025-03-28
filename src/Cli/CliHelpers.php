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
			self::cliLogAlert($errorMessage, 'error');
			WP_CLI::halt(1);
		} catch (ExitException $e) {
			exit("{$e->getCode()}: {$e->getMessage()}"); // phpcs:ignore Eightshift.Security.HelpersEscape.OutputNotEscaped
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
	 * @param string $heading Alert heading.
	 *
	 * @return void
	 */
	protected static function cliLogAlert(string $msg, string $type = 'success', string $heading = ''): void
	{
		$colorToUse = '%g';
		$defaultHeading = \__('Success', 'eightshift-libs');

		switch ($type) {
			case 'warning':
				$colorToUse = '%y';
				$defaultHeading = \__('Warning', 'eightshift-libs');
				break;
			case 'info':
				$colorToUse = '%B';
				$defaultHeading = \__('Info', 'eightshift-libs');
				break;
			case 'error':
				$colorToUse = '%R';
				$defaultHeading = \__('Something went wrong', 'eightshift-libs');
				break;
		}

		$headingToUse = empty($heading) ? $defaultHeading : $heading;

		if (\strpos($msg, '\n') !== false) {
			$output = "{$colorToUse}╭\n";
			$output .= "│ {$headingToUse}\n";

			foreach (\explode('\n', $msg) as $line) {
				$modifiedLine = \trim($line);
				$output .= "{$colorToUse}│ %n{$modifiedLine}\n";
			}

			$output .= "{$colorToUse}╰%n";
		} elseif (\preg_match('/\n/', $msg)) {
			$output = "{$colorToUse}╭\n";
			$output .= "│ {$headingToUse}\n";

			foreach (\explode("\n", $msg) as $line) {
				$modifiedLine = \trim($line);
				$output .= "{$colorToUse}│ %n{$modifiedLine}\n";
			}

			$output .= "{$colorToUse}╰%n";
		} else {
			$output = "{$colorToUse}╭\n";
			$output .= "│ {$headingToUse}\n";
			$output .= "│ %n{$msg}{$colorToUse}\n";
			$output .= "╰%n";
		}

		// Handle commands/code.
		$output = \preg_replace('/`(.*)`/', '%_$1%n', $output);

		WP_CLI::log(WP_CLI::colorize($output));
	}

	/**
	 * Return longdesc output for cli.
	 * Removes tabs and replaces them with space.
	 * Adds new line before and after ## heading.
	 *
	 * @param string $string String to convert.
	 *
	 * @return string
	 */
	public static function prepareLongDesc(string $string): string
	{
		return \preg_replace('/(##+)(.*)/m', "\n" . '${1}${2}' . "\n", \preg_replace('/\s*^\s*/m', "\n", \trim($string)));
	}
}
