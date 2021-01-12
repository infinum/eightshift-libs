<?php

/**
 * Class that registers WPCLI command for Config.
 *
 * @package EightshiftLibs\Config
 */

declare(strict_types=1);

namespace EightshiftLibs\Config;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class ConfigCli
 */
class ConfigCli extends AbstractCli
{

	/**
	 * Output dir relative path.
	 */
	public const OUTPUT_DIR = 'src/Config';

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
			'name' => $args[1] ?? 'Boilerplate',
			'version' => $args[2] ?? '1',
			'prefix' => $args[3] ?? 'ebs',
			'routes_version' => $args[5] ?? 'v2',
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
			'shortdesc' => 'Generates project config class.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'name',
					'description' => 'Define project name.',
					'optional' => true,
				],
				[
					'type' => 'assoc',
					'name' => 'version',
					'description' => 'Define project version.',
					'optional' => true,
				],
				[
					'type' => 'assoc',
					'name' => 'prefix',
					'description' => 'Define project prefix.',
					'optional' => true,
				],
				[
					'type' => 'assoc',
					'name' => 'routes_version',
					'description' => 'Define project REST version.',
					'optional' => true,
				],
			],
		];
	}

	public function __invoke(array $args, array $assocArgs) // phpcs:ignore
	{
		// Get Props.
		$name = $assocArgs['name'] ?? '';
		$version = $assocArgs['version'] ?? '';
		$prefix = $assocArgs['prefix'] ?? '';
		$env = $assocArgs['env'] ?? '';
		$routesVersion = $assocArgs['routes_version'] ?? '';

		$className = $this->getClassShortName();

		// Read the template contents, and replace the placeholders with provided variables.
		$class = $this->getExampleTemplate(__DIR__, $className);

		// Replace stuff in file.
		$class = $this->renameClassName($className, $class);

		$class = $this->renameNamespace($assocArgs, $class);

		$class = $this->renameUse($assocArgs, $class);

		if (!empty($name)) {
			$class = str_replace('eightshift-libs', $name, $class);
		}

		if (!empty($version)) {
			$class = str_replace('1.0.0', $version, $class);
		}

		if (!empty($prefix)) {
			$class = str_replace("'eb'", "'{$prefix}'", $class);
		}

		if (!empty($routesVersion)) {
			$class = str_replace('v1', $routesVersion, $class);
		}

		// Output final class to new file/folder and finish.
		$this->outputWrite(static::OUTPUT_DIR, $className, $class, $assocArgs);
	}
}
