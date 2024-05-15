<?php

/**
 * Class that registers WPCLI command for Blocks Components.
 *
 * @package EightshiftLibs\Blocks
 */

declare(strict_types=1);

namespace EightshiftLibs\Blocks;

use EightshiftLibs\Cli\ParentGroups\CliBlocks;
use EightshiftLibs\Helpers\Helpers;

/**
 * Class UseComponentCli
 */
class UseComponentCli extends AbstractBlocksCli
{
	/**
	 * Command name.
	 *
	 * @var string
	 */
	public const COMMAND_NAME = 'use-component';

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
		return self::COMMAND_NAME;
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
			'shortdesc' => 'Copy component from our library to your project.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'name',
					'description' => 'Specify component name. You can specify multiple components by separating them with a comma.',
					'optional' => false,
				],
			],
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Used to copy pre-created component from our library to your project. After copying you can modify the component in any way you see fit.

				## EXAMPLES

				# Copy component by name:
				$ wp {$this->commandParentName} {$this->getCommandParentName()} {$this->getCommandName()} --name='paragraph'

				# Copy multiple components by name:
				$ wp {$this->commandParentName} {$this->getCommandParentName()} {$this->getCommandName()} --name='paragraph, heading'

				## RESOURCES

				All our components can be found here:
				https://github.com/infinum/eightshift-frontend-libs/tree/develop/blocks/init/src/Blocks/components
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		$assocArgs = $this->prepareArgs($assocArgs);

		$this->getIntroText($assocArgs);

		$this->moveItems(
			$assocArgs,
			Helpers::getProjectPaths('blocksSourceComponents'),
			Helpers::getProjectPaths('blocksDestinationComponents'),
			'component',
			false,
			Helpers::getProjectPaths('blocksPrivateSourceCustom')
		);

		if (!$assocArgs[self::ARG_GROUP_OUTPUT]) {
			$this->getAssetsCommandText();
		}
	}
}
