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
	public const OUTPUT_DIR = 'src' . \DIRECTORY_SEPARATOR . 'ThemeOptions';

	/**
	 * Get WPCLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Generates project Theme Options class using ACF.',
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
			->renameTextDomain($assocArgs)
			->outputWrite(static::OUTPUT_DIR, $className, $assocArgs);
	}
}
