<?php

/**
 * Class that registers WPCLI command for ThemeOptions using ACF.
 *
 * @package EightshiftLibs\ThemeOptions
 */

declare(strict_types=1);

namespace EightshiftLibs\ThemeOptions;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class ThemeOptionsCli
 */
class ThemeOptionsCli extends AbstractCli
{

	/**
	 * Output dir relative path.
	 */
	public const OUTPUT_DIR = 'src/ThemeOptions';

	/**
	 * Get WPCLI command doc.
	 *
	 * @return array
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Generates project Theme Options class using ACF.',
		];
	}

	public function __invoke(array $args, array $assocArgs) // phpcs:ignore
	{
		$className = $this->getClassShortName();

		// Read the template contents, and replace the placeholders with provided variables.
		$class = $this->getExampleTemplate(__DIR__, $className);

		// Replace stuff in file.
		$class = $this->renameClassName($className, $class);
		$class = $this->renameNamespace($assocArgs, $class);
		$class = $this->renameUse($assocArgs, $class);
		$class = $this->renameTextDomain($assocArgs, $class);

		// Output final class to new file/folder and finish.
		$this->outputWrite(static::OUTPUT_DIR, $className, $class, $assocArgs);
	}
}
