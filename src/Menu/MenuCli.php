<?php

/**
 * Class that registers WPCLI command for Menu.
 *
 * @package EightshiftLibs\Menu
 */

declare(strict_types=1);

namespace EightshiftLibs\Menu;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class MenuCli
 */
class MenuCli extends AbstractCli
{

	/**
	 * Output dir relative path.
	 */
	public const OUTPUT_DIR = 'src/Menu';

	/**
	 * Get WPCLI command doc.
	 *
	 * @return array
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Generates menu class.',
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
			->renameTextDomain($assocArgs)
			->outputWrite(static::OUTPUT_DIR, $className, $assocArgs);
	}
}
