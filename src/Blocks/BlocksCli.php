<?php

/**
 * Class that registers WPCLI command for Blocks.
 *
 * @package EightshiftLibs\Blocks
 */

declare(strict_types=1);

namespace EightshiftLibs\Blocks;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliBlocks;
use EightshiftLibs\Helpers\Components;

/**
 * Class BlocksCli
 */
class BlocksCli extends AbstractCli
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
		return 'create_blocks_class';
	}

	/**
	 * Get WPCLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Create blocks service class.',
			'longdesc' => "
				This file is a main entrypoint for all our block editor setup.
				We use it to register all blocks, limit what blocks user can see, and lots more.

				## EXAMPLES
				$ wp boilerplate create_blocks
			",
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		$this->getIntroText($assocArgs);

		$className = $this->getClassShortName();

		$class = $this->getExampleTemplate(__DIR__, $className);

		// Read the template contents, and replace the placeholders with provided variables.
		$class->renameClassName($className)
			->renameNamespace($assocArgs)
			->renameTextDomainFrontendLibs($assocArgs)
			->renameUse($assocArgs)
			->outputWrite(Components::getProjectPaths('blocksDestination'), "{$className}.php", $assocArgs);
	}
}
