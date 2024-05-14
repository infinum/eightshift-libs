<?php

/**
 * Class that registers WPCLI command for Blocks Manifest.
 *
 * @package EightshiftLibs\Blocks
 */

declare(strict_types=1);

namespace EightshiftLibs\Blocks;

use EightshiftLibs\Cli\ParentGroups\CliBlocks;
use EightshiftLibs\Helpers\Helpers;
use WP_CLI;

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
		return 'use-manifest';
	}

	/**
	 * Get WPCLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Copy global settings manifest.json file from our library to your project.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'group_output',
					'optional' => true,
					'defaut' => false,
				],
			],
			'longdesc' => $this->prepareLongDesc("
				This file is a block editor's main setting file where you can find the color, options, and much more.

				## EXAMPLES

				# Copy manifest.json:
					$ wp {$this->commandParentName} {$this->getCommandParentName()} {$this->getCommandName()}


				## RESOURCES

				Manifest.json file will be created from this file:
				https://github.com/infinum/eightshift-frontend-libs/tree/develop/blocks/init/src/Blocks/manifest.json
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		$groupOutput = $assocArgs['group_output'] ?? false;

		$assocArgs = $this->prepareArgs($assocArgs);

		$this->getIntroText($assocArgs);

		$this->moveItems(
			\array_merge(
				$assocArgs,
				[
					'name' => 'manifest.json',
				],
			),
			Helpers::getProjectPaths('blocksSource'),
			Helpers::getProjectPaths('blocksDestination'),
			'file'
		);

		if (!$groupOutput) {
			WP_CLI::log('--------------------------------------------------');

			$this->cliLog('Please run `npm start` again to make sure everything works correctly.', 'M');
		}
	}
}
