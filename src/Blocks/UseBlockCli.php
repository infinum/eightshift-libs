<?php

/**
 * Class that registers WPCLI command for Blocks Block.
 *
 * @package EightshiftLibs\Blocks
 */

declare(strict_types=1);

namespace EightshiftLibs\Blocks;

use EightshiftLibs\Cli\ParentGroups\CliBlocks;
use EightshiftLibs\Helpers\Components;
use WP_CLI;

/**
 * Class UseBlockCli
 */
class UseBlockCli extends AbstractBlocksCli
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
		return 'use_block';
	}

	/**
	 * Define default arguments.
	 *
	 * @return array<string, int|string|boolean>
	 */
	public function getDefaultArgs(): array
	{
		return [
			'name' => 'button',
		];
	}

	/**
	 * Get WPCLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Copy block from our library to your project.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'name',
					'description' => 'Specify block name.',
					'optional' => false,
				],
			],
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Used to copy pre-created block from our library to your project. After copying you can modify the block in any way you see fit.

				## EXAMPLES

				# Copy block by name.
				$ wp boilerplate {$this->getCommandParentName()} {$this->getCommandName()} --name='paragraph'

				## RESOURCES

				All our blocks can be found here:
				https://github.com/infinum/eightshift-frontend-libs/tree/develop/blocks/init/src/Blocks/custom
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs) // phpcs:ignore Eightshift.Commenting.FunctionComment.WrongStyle
	{
		$groupOutput = $assocArgs['groupOutput'] ?? false;

		$this->moveItems(
			$assocArgs,
			Components::getProjectPaths('blocksSourceCustom'),
			Components::getProjectPaths('blocksDestinationCustom'),
			'block'
		);

		if (!$groupOutput) {
			WP_CLI::log('--------------------------------------------------');

			$this->cliLog('Please run `npm start` again to make sure everything works correctly.', "C");
		}
	}
}
