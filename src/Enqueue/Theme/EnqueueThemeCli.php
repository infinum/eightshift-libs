<?php

/**
 * Class that registers WPCLI command for Blocks.
 *
 * @package EightshiftLibs\Enqueue\Theme
 */

declare(strict_types=1);

namespace EightshiftLibs\Enqueue\Theme;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class EnqueueThemeCli
 */
class EnqueueThemeCli extends AbstractCli
{
	/**
	 * Output dir relative path.
	 */
	public const OUTPUT_DIR = 'src' . DIRECTORY_SEPARATOR . 'Enqueue' . DIRECTORY_SEPARATOR . 'Theme';

	/**
	 * Get WPCLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Generates Enqueue Theme class.',
		];
	}

	/* @phpstan-ignore-next-line */
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
