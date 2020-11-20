<?php

/**
 * Class that registers WPCLI command for Main.
 *
 * @package EightshiftLibs\Main
 */

declare(strict_types=1);

namespace EightshiftLibs\Main;

use EightshiftLibs\Cli\AbstractCli;
use WP_CLI\ExitException;

/**
 * Class MainCli
 */
class MainCli extends AbstractCli
{

	/**
	 * Output dir relative path.
	 */
	public const OUTPUT_DIR = 'src/Main';

	/**
	 * Get WPCLI command doc.
	 *
	 * @return array
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Generates Main class file for all other features using service container pattern.',
		];
	}

	public function __invoke(array $args, array $assocArgs) // phpcs:ignore
	{
		// Read the template contents, and replace the placeholders with provided variables.
		try {
			$class = $this->getExampleTemplate(__DIR__, $this->getClassShortName());
		} catch (ExitException $e) {
			exit("{$e->getCode()}: {$e->getMessage()}");
		}

		// Replace stuff in file.
		$class = $this->renameClassName($this->getClassShortName(), $class);

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
			$this->outputWrite(static::OUTPUT_DIR, $this->getClassShortName(), $class, $assocArgs);
		} catch (ExitException $e) {
			exit("{$e->getCode()}: {$e->getMessage()}");
		}
	}
}
