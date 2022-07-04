<?php

/**
 * Class that registers WPCLI command for Blocks Variations.
 *
 * @package EightshiftLibs\Blocks
 */

declare(strict_types=1);

namespace EightshiftLibs\Blocks;

use EightshiftLibs\Cli\ParentGroups\CliBlocks;
use EightshiftLibs\Helpers\Components;
use WP_CLI;

/**
 * Class UseVariationCli
 */
class UseVariationCli extends AbstractBlocksCli
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
		return 'use_variation';
	}

	/**
	 * Define default arguments.
	 *
	 * @return array<string, int|string|boolean>
	 */
	public function getDefaultArgs(): array
	{
		return [
			'name' => 'button-block',
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
			'shortdesc' => 'Copy variation from our library to your project.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'name',
					'description' => 'Specify variation name.',
					'optional' => false,
				],
			],
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Used to copy pre-created variation from our library to your project. After copying you can modify the variation in any way you see fit.

				## EXAMPLES

				# Copy variation by name.
				$ wp boilerplate {$this->getCommandParentName()} {$this->getCommandName()} --name='button-block'

				## RESOURCES

				All our variations can be found here:
				https://github.com/infinum/eightshift-frontend-libs/tree/develop/blocks/init/src/Blocks/variations
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		$groupOutput = $assocArgs['groupOutput'] ?? false;

		$this->moveItems(
			$assocArgs,
			Components::getProjectPaths('blocksSourceVariations'),
			Components::getProjectPaths('blocksDestinationVariations'),
		);

		if (!$groupOutput) {
			WP_CLI::log('--------------------------------------------------');

			$this->cliLog('Please run `npm start` again to make sure everything works correctly.', "C");
		}
	}
}
