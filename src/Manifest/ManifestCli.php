<?php

/**
 * Class that registers WPCLI command for Manifest.
 *
 * @package EightshiftLibs\Manifest
 */

declare(strict_types=1);

namespace EightshiftLibs\Manifest;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class ManifestCli
 */
class ManifestCli extends AbstractCli
{

	/**
	 * Output dir relative path.
	 */
	public const OUTPUT_DIR = 'src/Manifest';

	/**
	 * Get WPCLI command doc.
	 *
	 * @return array
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Generates Manifest class.',
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

		// Output final class to new file/folder and finish.
		$this->outputWrite(static::OUTPUT_DIR, $className, $class, $assocArgs);
	}
}
