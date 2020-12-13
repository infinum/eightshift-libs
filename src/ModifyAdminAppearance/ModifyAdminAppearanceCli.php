<?php

/**
 * Class that registers WPCLI command for ModifyAdminAppearance.
 *
 * @package EightshiftLibs\ModifyAdminAppearance
 */

declare(strict_types=1);

namespace EightshiftLibs\ModifyAdminAppearance;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class ModifyAdminAppearanceCli
 */
class ModifyAdminAppearanceCli extends AbstractCli
{

	/**
	 * Output dir relative path.
	 */
	public const OUTPUT_DIR = 'src/ModifyAdminAppearance';

	/**
	 * Get WPCLI command doc.
	 *
	 * @return array
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Generates Modify Admin Appearance class.',
		];
	}

	public function __invoke(array $args, array $assocArgs) // phpcs:ignore
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
