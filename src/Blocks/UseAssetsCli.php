<?php

/**
 * Class that registers WP-CLI command for Blocks assets.
 *
 * @package EightshiftLibs\Blocks
 */

declare(strict_types=1);

namespace EightshiftLibs\Blocks;

use EightshiftLibs\Cli\ParentGroups\CliBlocks;
use EightshiftLibs\Helpers\Components;
use WP_CLI;

/**
 * Class UseAssetsCli
 */
class UseAssetsCli extends AbstractBlocksCli
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
		return 'use-assets';
	}

	/**
	 * Get WPCLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Copy assets from our library to your project.',
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Used to copy pre-created assets from our library to your project. After copying you can modify it in any way you see fit.

				## EXAMPLES

				# Copy assets:
				$ wp {$this->commandParentName} {$this->getCommandParentName()} {$this->getCommandName()}

				## RESOURCES

				Assets will be created from this folder:
				https://github.com/infinum/eightshift-frontend-libs/tree/develop/blocks/init/src/Blocks/assets
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		$this->getIntroText($assocArgs);

		$groupOutput = $assocArgs['groupOutput'] ?? false;

		$this->moveItems(
			\array_merge(
				$assocArgs,
				[
					'name' => 'assets',
				],
			),
			Components::getProjectPaths('blocksAssetsSource'),
			Components::getProjectPaths('blocksAssetsDestination'),
			'assets',
			true
		);

		if (!$groupOutput) {
			WP_CLI::log('--------------------------------------------------');
			$this->cliLog('Please run `npm start` again to make sure everything works correctly.', "M");
		}
	}
}
