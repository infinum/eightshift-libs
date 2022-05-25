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

/**
 * Class ModifyAdminAppearanceCli
 */
class ModifyAdminAppearanceCli extends AbstractCli
{
	/**
	 * Output dir relative path.
	 */
	public const OUTPUT_DIR = 'src' . \DIRECTORY_SEPARATOR . 'ModifyAdminAppearance';

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
		return 'modify_admin_appearance';
	}

	/**
	 * Get WPCLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Create modify admin appearance class.',
			'longdesc' => $this->prepareLongDesc("
			## USAGE

			Used to create admin appearance service class to register features that change the admin based on the environment like colors.

			## EXAMPLES

			# Create service class:
			$ wp boilerplate {$this->getCommandParentName()} {$this->getCommandName()}

			## RESOURCES

			Service class will be created from this example:
			https://github.com/infinum/eightshift-libs/blob/develop/src/ModifyAdminAppearance/ModifyAdminAppearanceExample.php
		"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		$className = $this->getClassShortName();

		// Read the template contents, and replace the placeholders with provided variables.
		$this->getExampleTemplate(__DIR__, $className)
			->renameClassName($className)
			->renameNamespace($assocArgs)
			->renameUse($assocArgs)
			->outputWrite(static::OUTPUT_DIR, $className, $assocArgs);
	}
}
