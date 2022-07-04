<?php

/**
 * Class that registers WPCLI command for Blocks Manifest.
 *
 * @package EightshiftLibs\Blocks
 */

declare(strict_types=1);

namespace EightshiftLibs\Blocks;

use EightshiftLibs\Cli\ParentGroups\CliBlocks;
use EightshiftLibs\Helpers\Components;

/**
 * Class UseManifestCli
 */
class UseManifestCli extends AbstractBlocksCli
{
	/**
	 * Get WPCLI command parent name
	 *
	 * @return string
	 */
	public function getCommandParentName(): string
	{
		return CliBlocks::COMMAND_NAME;
	}

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'use_manifest';
	}

	/**
	 * Get WPCLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Create blocks manifest.json file.',
			'longdesc' => "
				This file is a block editor main setting file where you can find color, option and much more.

				## EXAMPLES
				$ wp boilerplate create manifest
			",
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		$this->moveItems(
			[
				'name' => 'manifest.json',
			],
			Components::getProjectPaths('blocksSource'),
			Components::getProjectPaths('blocksDestination')
		);
	}
}
