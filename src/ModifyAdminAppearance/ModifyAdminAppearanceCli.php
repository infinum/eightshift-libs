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
	 * @return string
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Generates Modify Admin Appearance class.',
		];
	}

	/**
	 * Create modify admin appearance class
	 *
	 * @param array $args      Array of arguments form terminal.
	 * @param array $assocArgs Array of associative arguments form terminal.
	 */
	public function __invoke(array $args, array $assocArgs)
	{

		$className = $this->getClassShortName();

		// Read the template contents, and replace the placeholders with provided variables.
		$class = $this->getExampleTemplate(__DIR__, $className);

		// Replace stuff in file.
		$class = $this->renameClassName($className, $class);
		$class = $this->renameNamespace($assocArgs, $class);
		$class = $this->renameUse($assocArgs, $class);

		// Output final class to new file/folder and finish.
		$this->outputWrite(static::OUTPUT_DIR, $className, $class);
	}
}
