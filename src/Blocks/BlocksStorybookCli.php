<?php

/**
 * Class that registers WPCLI command for Blocks Storybook.
 *
 * @package EightshiftLibs\Blocks
 */

declare(strict_types=1);

namespace EightshiftLibs\Blocks;

use EightshiftLibs\Cli\ParentGroups\CliBlocks;
use EightshiftLibs\Helpers\Components;
use WP_CLI;

/**
 * Class BlocksStorybookCli
 */
class BlocksStorybookCli extends AbstractBlocksCli
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
		return 'blocks_storybook';
	}

	/**
	 * Get WPCLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Setup storybook in your project.',
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Used to copy all configuration files to your project needed to run Storybook.

				## EXAMPLES

				# Create Storybook config:
				$ wp boilerplate {$this->getCommandParentName()} {$this->getCommandName()}

				## RESOURCES

				Storybook config will be created from this folder:
				https://github.com/infinum/eightshift-frontend-libs/tree/develop/blocks/init/storybook
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		$this->moveItems(
			[
				'name' => 'storybook',
			],
			Components::getProjectPaths('blocksStorybookSource'),
			Components::getProjectPaths('blocksStorybookDestination'),
			true
		);

		WP_CLI::log('--------------------------------------------------');

		WP_CLI::success('Please run `npm start` again to make sure everything works correctly.');
	}
}
