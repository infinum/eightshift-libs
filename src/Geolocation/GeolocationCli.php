<?php

// phpcs:ignoreFile Generic.Files.LineLength.TooLong

/**
 * Class that registers WPCLI command for Geolocation creation.
 *
 * @package EightshiftLibs\Geolocation
 */

declare(strict_types=1);

namespace EightshiftLibs\Geolocation;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliCreate;

/**
 * Class GeolocationCli
 */
class GeolocationCli extends AbstractCli
{
	/**
	 * Output dir relative path.
	 *
	 * @var string
	 */
	public const OUTPUT_DIR = 'src' . \DIRECTORY_SEPARATOR . 'Geolocation';

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
		return 'geolocation';
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
		return [
			'cookie_name' => 'es-geolocation-test',
		];
	}

	/**
	 * Define default arguments.
	 *
	 * @return array<string, mixed>
	 */
	public function getDefaultArgs(): array
	{
		return [
			'cookie_name' => 'es-geolocation',
		];
	}

	/**
	 * Get WPCLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Create geolocation service class.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'cookie_name',
					'description' => 'Cookie name that will be used to store geolocation details.',
					'optional' => true,
					'default' => $this->getDefaultArg('cookie_name'),
				],
			],
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Used to create geolocation detection stored in users cookie based on the GeoLite2-Country database.
				You must provide the database and phar file in the project.

				## EXAMPLES

				# Create service class:
				$ wp boilerplate {$this->getCommandParentName()} {$this->getCommandName()}

				# Create service class with custom cookie name:
				$ wp boilerplate {$this->getCommandParentName()} {$this->getCommandName()} --cookie_name='test_cookie'

				## RESOURCES

				Service class will be created from this example:
				https://github.com/infinum/eightshift-libs/blob/develop/src/Geolocation/GeolocationExample.php
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		// Get Arguments.
		$cookie_name = $this->getArg($assocArgs, 'cookie_name');

		// Get full class name.
		$className = $this->getClassShortName();

		// Read the template contents, and replace the placeholders with provided variables.
		$class = $this->getExampleTemplate(__DIR__, $className)
			->renameClassName($className)
			->renameNamespace($assocArgs)
			->renameUse($assocArgs)
			->renameTextDomain($assocArgs)
			->searchReplaceString($this->getArgTemplate('cookie_name'), $cookie_name);

		// Output final class to new file/folder and finish.
		$class->outputWrite(static::OUTPUT_DIR, $className, $assocArgs);
	}
}
