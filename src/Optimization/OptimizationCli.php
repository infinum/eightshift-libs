<?php

/**
 * Class that registers WPCLI command for ModifyAdminAppearance.
 *
 * @package EightshiftLibs\ModifyAdminAppearance
 */

declare(strict_types=1);

namespace EightshiftLibs\ModifyAdminAppearance;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliCreate;
use EightshiftLibs\Helpers\Helpers;

/**
 * Class OptimizationCli
 */
class OptimizationCli extends AbstractCli
{
	/**
	 * Get WPCLI command parent name
	 *
	 * @return string
	 */
	public function getCommandParentName(): string
	{
		return CliCreate::COMMAND_NAME;
	}

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'optimization';
	}

	/**
	 * Get WPCLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Create optimization class.',
			'longdesc' => $this->prepareLongDesc("
			## USAGE

			Used to create optimization service class remove unecesery WP core stuff for faster loading.

			## EXAMPLES

			# Create service class:
			$ wp {$this->commandParentName} {$this->getCommandParentName()} {$this->getCommandName()}

			## RESOURCES

			Service class will be created from this example:
			https://github.com/infinum/eightshift-libs/blob/develop/src/Optimization/OptimizationExample.php
		"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		$assocArgs = $this->prepareArgs($assocArgs);

		$this->getIntroText($assocArgs);

		$className = $this->getClassShortName();

		// Read the template contents, and replace the placeholders with provided variables.
		$this->getExampleTemplate(__DIR__, $className)
			->renameClassName($className)
			->renameGlobals($assocArgs)
			->outputWrite(Helpers::getProjectPaths('srcDestination', 'Optimization'), "{$className}.php", $assocArgs);
	}
}
