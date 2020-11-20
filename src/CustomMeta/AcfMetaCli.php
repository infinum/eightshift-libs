<?php

/**
 * Class that registers WPCLI command for Custom Acf Meta Fields.
 *
 * @package EightshiftLibs\CustomMeta
 */

declare(strict_types=1);

namespace EightshiftLibs\CustomMeta;

use EightshiftLibs\Cli\AbstractCli;
use WP_CLI\ExitException;

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
		try {
			$class = $this->getExampleTemplate(__DIR__, $this->getClassShortName());
		} catch (ExitException $e) {
			exit("{$e->getCode()}: {$e->getMessage()}");
		}

		// Replace stuff in file.
		$class = $this->renameClassNameWithPrefix($this->getClassShortName(), $className, $class);
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
