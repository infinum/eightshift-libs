<?php

/**
 * Class that registers WPCLI command for Blocks Wrapper.
 *
 * @package EightshiftLibs\Blocks
 */

declare(strict_types=1);

namespace EightshiftLibs\Blocks;

use EightshiftLibs\Cli\ParentGroups\CliBlocks;
use EightshiftLibs\Helpers\Helpers;

/**
 * Class UseWrapperCli
 */
class UseWrapperCli extends AbstractBlocksCli
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
		return 'use-wrapper';
	}

	/**
	 * Get WPCLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Copy wrapper from our library to your project.',
			'longdesc' => $this->prepareLongDesc("
				## EXAMPLES

				# Copy wrapper:
				$ wp {$this->commandParentName} {$this->getCommandParentName()} {$this->getCommandName()}

				## RESOURCES

				Our wrapper can be found here:
				https://github.com/infinum/eightshift-frontend-libs/tree/develop/blocks/init/src/Blocks/wrapper
				or
				https://github.com/infinum/eightshift-frontend-libs-tailwind/tree/develop/blocks/init/src/Blocks/wrapper
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
					'name' => 'wrapper',
				],
			),
			$this->isTailwind() ? Helpers::getProjectPaths('blocksSourceTailwindWrapper') : Helpers::getProjectPaths('blocksSourceWrapper'),
			Helpers::getProjectPaths('blocksDestinationWrapper'),
			'wrapper',
			true
		);

		if (!$assocArgs[self::ARG_GROUP_OUTPUT]) {
			$this->getAssetsCommandText();
		}
	}
}
