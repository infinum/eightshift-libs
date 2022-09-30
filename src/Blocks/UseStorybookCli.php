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
 * Class UseStorybookCli
 */
class UseStorybookCli extends AbstractBlocksCli
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
		return 'use-storybook';
	}

	/**
	 * Get WPCLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Copy storybook config from our library to your project.',
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Used to copy all configuration files to your project needed to run Storybook.

				## INSTALATION

				After you run the cli command please follow these steps:
				{$this->getCommonAfterIntallInstructions()}

				## EXAMPLES

				# Create Storybook config:
				$ wp {$this->commandParentName} {$this->getCommandParentName()} {$this->getCommandName()}

				## RESOURCES

				Storybook config will be created from this folder:
				https://github.com/infinum/eightshift-frontend-libs/tree/develop/blocks/init/storybook
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
					'name' => 'storybook',
				],
			),
			Components::getProjectPaths('blocksStorybookSource'),
			Components::getProjectPaths('blocksStorybookDestination'),
			'storybook',
			true
		);

		if (!$groupOutput) {
			WP_CLI::log('--------------------------------------------------');

			$this->cliLog('Please follow these steps to complete your Storybook setup:', 'M');
			$this->cliLog($this->prepareLongDesc($this->getCommonAfterIntallInstructions()), 'M');
		}
	}

	/**
	 * Common instalation steps.
	 *
	 * @return string
	 */
	private function getCommonAfterIntallInstructions(): string
	{
		return '
			1. Run `npm install @eightshift/storybook --save-dev` command in the terminal to install the storybook package.
			2. Open package.json.
			3. Add to scripts: `"storybookBuild": "build-storybook -s public -o storybook"`
			4. Add to scripts: `"storybook": "start-storybook -s public"`
			5. Start storybook by running this command `npm run storybook`.
		';
	}
}
