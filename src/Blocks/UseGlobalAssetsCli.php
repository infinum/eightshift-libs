<?php

/**
 * Class that registers WPCLI command for Blocks global assets.
 *
 * @package EightshiftLibs\Blocks
 */

declare(strict_types=1);

namespace EightshiftLibs\Blocks;

use EightshiftLibs\Cli\ParentGroups\CliBlocks;
use EightshiftLibs\Helpers\Helpers;

/**
 * Class UseGlobalAssetsCli
 */
class UseGlobalAssetsCli extends AbstractBlocksCli
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
		return 'use-global-assets';
	}

	/**
	 * Get WPCLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Copy global assets from our library to your project.',
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Used to copy pre-created global assets from our library to your project. After copying you can modify them in any way you see fit.

				## EXAMPLES

				# Copy global assets.
				$ wp {$this->commandParentName} {$this->getCommandParentName()} {$this->getCommandName()}

				## RESOURCES

				Global assets will be created from this folder:
				https://github.com/infinum/eightshift-frontend-libs/tree/develop/blocks/init/assets
				or
				https://github.com/infinum/eightshift-frontend-libs-tailwind/tree/develop/blocks/init/assets
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		$assocArgs = $this->prepareArgs($assocArgs);

		$this->getIntroText($assocArgs);

		$this->moveItems(
			\array_merge(
				$assocArgs,
				[
					'name' => 'assets',
				],
			),
			$this->isTailwind() ? Helpers::getProjectPaths('blocksGlobalAssetsTailwindSource') : Helpers::getProjectPaths('blocksGlobalAssetsSource'),
			Helpers::getProjectPaths('blocksGlobalAssetsDestination'),
			'assets folder',
			true
		);

		if (!$assocArgs[self::ARG_GROUP_OUTPUT]) {
			$this->getAssetsCommandText();
		}
	}
}
