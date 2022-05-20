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

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use WP_CLI;

/**
 * Class CliReset
 */
class CliReset extends AbstractCli
{

	/**
	 * Get WPCLI command parent name
	 *
	 * @return string
	 */
	public function getCommandParentName(): string
	{
		return 'develop';
	}

	/**
	 * Get WPCLI command name
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
		$dir = $this->getOutputDir('');

		if (!\is_dir($dir)) {
			WP_CLI::error('Output directory is not a directory.');
		}
	
		$iterator = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
		$files = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST);
	
		foreach ($files as $file) {
			if ($file->isDir()) {
				rmdir($file->getRealPath());
			} else {
				unlink($file->getRealPath());
			}
		}
	
		rmdir($dir);

		WP_CLI::success('Output directory successfully removed.');
	}
}
