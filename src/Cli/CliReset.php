<?php

/**
 * Class that registers WPCLI command for Development Reset.
 * Only used for development and can't be called via WPCLI.
 * It will delete CLI output directory.
 *
 * @package EightshiftLibs\Cli
 */

declare(strict_types=1);

namespace EightshiftLibs\Cli;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class CliReset
 */
class CliReset extends AbstractCli
{

	/**
	 * Get WPCLI command name.
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'reset';
	}

	/**
	 * Removes the directory
	 *
	 * @param array $args      Array of arguments form terminal.
	 * @param array $assocArgs Array of associative arguments form terminal.
	 */
	public function __invoke(array $args, array $assocArgs)
	{
		$output_dir = $this->getOutputDir('');

		system('rm -rf ' . escapeshellarg($output_dir));

		\WP_CLI::success('Output directory successfully removed.');
	}
}
