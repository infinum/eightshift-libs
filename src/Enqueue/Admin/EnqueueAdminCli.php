<?php

/**
 * Class that registers WPCLI command for Blocks.
 *
 * @package EightshiftLibs\Enqueue\Admin
 */

declare(strict_types=1);

namespace EightshiftLibs\Enqueue\Admin;

use EightshiftLibs\Cli\AbstractCli;

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
	 * @return string
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Generates Enqueue Admin class.',
		];
	}

	public function __invoke(array $args, array $assocArgs ) // phpcs:ignore Squiz.Commenting.FunctionComment.Missing, Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClassBeforeLastUsed
	{

		$className = $this->getClassShortName();

		// Read the template contents, and replace the placeholders with provided variables.
		$class = $this->getExampleTemplate(__DIR__, $className);

		// Replace stuff in file.
		$class = $this->renameClassName($className, $class);
		$class = $this->renameNamespace($assocArgs, $class);
		$class = $this->renameUse($assocArgs, $class);

		// Output final class to new file/folder and finish.
		$this->outputWrite(static::OUTPUT_DIR, $className, $class);
	}
}
