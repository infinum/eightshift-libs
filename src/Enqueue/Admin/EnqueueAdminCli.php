<?php

/**
 * Class that registers WPCLI command for Blocks.
 *
 * @package EightshiftLibs\Enqueue\Admin
 */

declare(strict_types=1);

namespace EightshiftLibs\Enqueue\Admin;

use EightshiftLibs\Cli\AbstractCli;
use WP_CLI\ExitException;

/**
 * Class EnqueueAdminCli
 */
class EnqueueAdminCli extends AbstractCli
{

	/**
	 * Output dir relative path.
	 */
	public const OUTPUT_DIR = 'src/Enqueue/Admin';

	/**
	 * Get WPCLI command doc.
	 *
	 * @return array
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Generates Enqueue Admin class.',
		];
	}

	public function __invoke(array $args, array $assocArgs) // phpcs:ignore
	{
		$className = $this->getClassShortName();

		// Read the template contents, and replace the placeholders with provided variables.
		try {
			$class = $this->getExampleTemplate(__DIR__, $className);
		} catch (ExitException $e) {
			exit("{$e->getCode()}: {$e->getMessage()}");
		}

		// Replace stuff in file.
		$class = $this->renameClassName($className, $class);

		try {
			$class = $this->renameNamespace($assocArgs, $class);
		} catch (ExitException $e) {
			exit("{$e->getCode()}: {$e->getMessage()}");
		}

		try {
			$class = $this->renameUse($assocArgs, $class);
		} catch (ExitException $e) {
			exit("{$e->getCode()}: {$e->getMessage()}");
		}

		// Output final class to new file/folder and finish.
		try {
			$this->outputWrite(static::OUTPUT_DIR, $className, $class, $assocArgs);
		} catch (ExitException $e) {
			exit("{$e->getCode()}: {$e->getMessage()}");
		}
	}
}
