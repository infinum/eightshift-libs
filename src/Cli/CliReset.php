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

use WP_CLI;

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
	 * Get WPCLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'DEVELOP - Used to reset and remove all outputs.',
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		$outputDir = $this->getOutputDir('');

		if (\is_dir($outputDir)) {
			\rmdir($outputDir);

			WP_CLI::success('Output directory successfully removed.');
		}
	}
}
