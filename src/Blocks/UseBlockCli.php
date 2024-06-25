<?php

/**
 * Class that registers WPCLI command for Blocks Block.
 *
 * @package EightshiftLibs\Blocks
 */

declare(strict_types=1);

namespace EightshiftLibs\Blocks;

use EightshiftLibs\Cli\ParentGroups\CliBlocks;
use EightshiftLibs\Helpers\Helpers;

/**
 * Class UseBlockCli
 */
class UseBlockCli extends AbstractBlocksCli
{
	/**
	 * Command name.
	 *
	 * @var string
	 */
	public const COMMAND_NAME = 'use-block';

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
			'shortdesc' => 'Copy block from our library to your project.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'name',
					'description' => 'Specify block name. You can specify multiple blocks by comma separator.',
					'optional' => false,
				],
			],
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Used to copy pre-created block from our library to your project. After copying you can modify the block in any way you see fit.

				## EXAMPLES

				# Copy block by name:
				$ wp {$this->commandParentName} {$this->getCommandParentName()} {$this->getCommandName()} --name='paragraph'

				# Copy multiple blocks by name:
				$ wp {$this->commandParentName} {$this->getCommandParentName()} {$this->getCommandName()} --name='paragraph, heading'

				## RESOURCES

				All our blocks can be found here:
				https://github.com/infinum/eightshift-frontend-libs/tree/develop/blocks/init/src/Blocks/custom
				or
				https://github.com/infinum/eightshift-frontend-libs-tailwind/tree/develop/blocks/init/src/Blocks/custom
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
			$this->isTailwind() ? Helpers::getProjectPaths('blocksSourceTailwindCustom') : Helpers::getProjectPaths('blocksSourceCustom'),
			Helpers::getProjectPaths('blocksDestinationCustom'),
			'block'
		);
	}
}
