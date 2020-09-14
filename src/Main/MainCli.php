<?php

/**
 * Class that registers WPCLI command for Main.
 *
 * @package EightshiftLibs\Main
 */

declare(strict_types=1);

namespace EightshiftLibs\Main;

use EightshiftLibs\Cli\AbstractCli;

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
	 * @return string
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Generates Main class file for all other features using service container pattern.',
		];
	}

	public function __invoke(array $args, array $assocArgs ) // phpcs:ignore Squiz.Commenting.FunctionComment.Missing, Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClassBeforeLastUsed
	{

		// Read the template contents, and replace the placeholders with provided variables.
		$class = $this->getExampleTemplate(__DIR__, $this->getClassShortName());

		// Replace stuff in file.
		$class = $this->renameClassName($this->getClassShortName(), $class);
		$class = $this->renameNamespace($assocArgs, $class);
		$class = $this->renameUse($assocArgs, $class);

		// Output final class to new file/folder and finish.
		$this->outputWrite(static::OUTPUT_DIR, $this->getClassShortName(), $class);
	}
}
