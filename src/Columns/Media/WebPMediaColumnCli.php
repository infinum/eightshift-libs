<?php

/**
 * Class that registers WPCLI command for WebPMediaColumn.
 *
 * @package EightshiftLibs\CustomMeta
 */

declare(strict_types=1);

namespace EightshiftLibs\Columns\Media;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class WebPMediaColumnCli.
 */
class WebPMediaColumnCli extends AbstractCli
{
	/**
	 * Output dir relative path.
	 */
	public const OUTPUT_DIR = 'src' . \DIRECTORY_SEPARATOR . 'Columns' . \DIRECTORY_SEPARATOR . 'Media';

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'create_webp_media_column';
	}

	/**
	 * Get WPCLI command doc.
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Generates columns class for media WebP images.',
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
