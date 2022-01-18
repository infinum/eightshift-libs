<?php

/**
 * Class that registers WPCLI command for Media.
 *
 * @package EightshiftLibs\Media
 */

declare(strict_types=1);

namespace EightshiftLibs\Media;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class MediaCli
 */
class MediaCli extends AbstractCli
{
	/**
	 * Output dir relative path.
	 */
	public const OUTPUT_DIR = 'src' . DIRECTORY_SEPARATOR . 'Media';

	/**
	 * Get WPCLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Generates media class.',
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
