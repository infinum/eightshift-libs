<?php

/**
 * Class that registers WPCLI command for LintPhpCli.
 *
 * @package EightshiftLibs\LintPhp
 */

declare(strict_types=1);

namespace EightshiftLibs\LintPhp;

use EightshiftLibs\Cli\AbstractCli;
use WP_CLI\ExitException;

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
	 * @return array
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Initialize Command for linting all you PHP files in the project before you commit to version control.',
		];
	}

	public function __invoke(array $args, array $assocArgs) // phpcs:ignore
	{
		$files = (string)shell_exec('git diff --cached --name-only --diff-filter=ACMR HEAD');

		preg_match_all('/.*.php/', $files, $matches);

		if (!$matches[0]) {
			\WP_CLI::warning('There are no files stashed to check using WPCS linter.');
		}

		$output = '';

		foreach ($matches[0] as $file) {
			$output .= (string)shell_exec("composer run standards:check {$file}");
		}

		\WP_CLI::log($output);

		if (preg_match('/ERROR/', $output) || preg_match('/WARNING/', $output)) {
			try {
				\WP_CLI::error('Please fix all linting issues before continuing.');
			} catch (ExitException $e) {
				exit("{$e->getCode()}: {$e->getMessage()}");
			}
		}

		\WP_CLI::success('Success! You have no linting issues.');
	}
}
