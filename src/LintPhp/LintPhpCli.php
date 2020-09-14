<?php

/**
 * Class that registers WPCLI command for LintPhpCli.
 *
 * @package EightshiftLibs\LintPhp
 */

declare(strict_types=1);

namespace EightshiftLibs\LintPhp;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class LintPhpCli
 */
class LintPhpCli extends AbstractCli
{

	/**
	 * Output dir relative path.
	 */
	public const OUTPUT_DIR = 'bin';

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'run_lint_php';
	}

	/**
	 * Get WPCLI command doc.
	 *
	 * @return string
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Initialize Command for linting all you PHP files in the project before you commit to version control.',
		];
	}

	public function __invoke(array $args, array $assocArgs ) // phpcs:ignore Squiz.Commenting.FunctionComment.Missing, Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClassBeforeLastUsed
	{
		$output = shell_exec('composer run standards:check'); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.system_calls_shell_exec

		\WP_CLI::log($output);

		if (preg_match('/ERROR/', $output) || preg_match('/WARNING/', $output)) {
			\WP_CLI::error('Please fix all linting issues before continuing.');
		}

		\WP_CLI::success('Success! You have no linting issues.');
	}
}
