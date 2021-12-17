<?php

/**
 * Class that registers WPCLI command for GdprSettings using ACF.
 *
 * @package EightshiftLibs\GdprSettings
 */

declare(strict_types=1);

namespace EightshiftLibs\GdprSettings;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class GdprSettingsCli
 */
class GdprSettingsCli extends AbstractCli
{
	public const COMMAND_NAME = 'create_gdpr_settings';

	/**
	 * Output dir relative path.
	 */
	public const OUTPUT_DIR = 'src' . DIRECTORY_SEPARATOR . 'GdprSettings';

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return self::COMMAND_NAME;
	}

	/**
	 * Get WPCLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Generates project GDPR Settings class using ACF.',
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs) // phpcs:ignore
	{
		$className = $this->getClassShortName();

		// Read the template contents, and replace the placeholders with provided variables.
		$this->getExampleTemplate(__DIR__, $className)
			->renameClassName($className)
			->renameNamespace($assocArgs)
			->renameUse($assocArgs)
			->renameTextDomain($assocArgs)
			->outputWrite(static::OUTPUT_DIR, $className, $assocArgs);
	}
}
