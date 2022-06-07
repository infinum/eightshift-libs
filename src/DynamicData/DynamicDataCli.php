<?php

/**
 * Class that registers WPCLI command for DynamicData.
 *
 * @package EightshiftLibs\DynamicData
 */

declare(strict_types=1);

namespace EightshiftLibs\DynamicData;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliCreate;

/**
 * Class DynamicDataCli
 */
class DynamicDataCli extends AbstractCli
{
	/**
	 * Output dir relative path.
	 */
	public const OUTPUT_DIR = 'src' . \DIRECTORY_SEPARATOR . 'DynamicData';

	/**
	 * Get WPCLI command parent name
	 *
	 * @return string
	 */
	public function getCommandParentName(): string
	{
		return CliCreate::COMMAND_NAME;
	}

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'dynamic_data';
	}

	/**
	 * Define default develop props.
	 *
	 * @param string[] $args WPCLI eval-file arguments.
	 *
	 * @return array<string, mixed>
	 */
	public function getDevelopArgs(array $args): array
	{
		return [];
	}

	/**
	 * Get WPCLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Generates projects dynamic data class.',
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		$className = $this->getClassShortName();

		// Read the template contents, and replace the placeholders with provided variables.
		$class = $this->getExampleTemplate(__DIR__, $className)
			->renameClassName($className)
			->renameNamespace($assocArgs)
			->renameUse($assocArgs);

		// Output final class to new file/folder and finish.
		$class->outputWrite(static::OUTPUT_DIR, $className, $assocArgs);
	}
}
