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
use EightshiftLibs\Helpers\Helpers;

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
		return 'create-blocks-class';
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
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				This file is a main entrypoint for all our block editor setup.
				It's used to register blocks, define which blocks are allowed, and more.

				## EXAMPLES

				# Create service class:
				$ wp {$this->commandParentName} {$this->getCommandParentName()} {$this->getCommandName()}

				## RESOURCES
				https://github.com/infinum/eightshift-libs/blob/develop/src/Blocks/BlocksExample.php
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		$assocArgs = $this->prepareArgs($assocArgs);
		$this->getIntroText($assocArgs);

		$className = $this->getClassShortName();

		$class = $this->getExampleTemplate(__DIR__, $className);

		// Read the template contents, and replace the placeholders with provided variables.
		$class->renameClassName($className)
			->renameGlobals($assocArgs)
			->outputWrite(Helpers::getProjectPaths('blocksDestination'), "{$className}.php", $assocArgs);
	}
}
