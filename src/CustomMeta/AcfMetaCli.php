<?php

/**
 * Class that registers WPCLI command for Custom Acf Meta Fields.
 *
 * @package EightshiftLibs\CustomMeta
 */

declare(strict_types=1);

namespace EightshiftLibs\CustomMeta;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class AcfMetaCli
 */
class AcfMetaCli extends AbstractCli
{

	/**
	 * Output dir relative path.
	 *
	 * @var string
	 */
	public const OUTPUT_DIR = 'src/CustomMeta';

	/**
	 * Define default develop props.
	 *
	 * @param array $args WPCLI eval-file arguments.
	 *
	 * @return array
	 */
	public function getDevelopArgs(array $args): array
	{
		return [
			'name' => $args[1] ?? 'title',
		];
	}

	/**
	 * Get WPCLI command doc.
	 *
	 * @return array
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Generates custom ACF meta fields class file.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'name',
					'description' => 'The name of the custom meta slug. Example: title.',
					'optional' => false,
				],
			],
		];
	}

	public function __invoke(array $args, array $assocArgs) // phpcs:ignore
	{
		// Get Props.
		$fieldName = $this->prepareSlug($assocArgs['name']);

		// Get full class name.
		$className = $this->getFileName($fieldName);
		$className = $className . $this->getClassShortName();

		// Read the template contents, and replace the placeholders with provided variables.
		$class = $this->getExampleTemplate(__DIR__, $this->getClassShortName());

		// Replace stuff in file.
		$class = $this->renameClassNameWithPrefix($this->getClassShortName(), $className, $class);
		$class = $this->renameNamespace($assocArgs, $class);

		$class = $this->renameUse($assocArgs, $class);

		// Output final class to new file/folder and finish.
		$this->outputWrite(static::OUTPUT_DIR, $className, $class, $assocArgs);
	}
}
