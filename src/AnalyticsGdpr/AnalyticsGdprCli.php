<?php

/**
 * Class that registers WPCLI command for AnalyticsGdpr using ACF.
 *
 * @package EightshiftLibs\AnalyticsGdpr
 */

declare(strict_types=1);

namespace EightshiftLibs\AnalyticsGdpr;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class AnalyticsGdprCli
 */
class AnalyticsGdprCli extends AbstractCli
{
	public const COMMAND_NAME = 'create_analytics_gdpr_settings';

	/**
	 * Output dir relative path.
	 */
	public const OUTPUT_DIR = 'src' . \DIRECTORY_SEPARATOR . 'AnalyticsGdpr';

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
			'shortdesc' => 'Generates project Analytics and GDPR Settings classes using ACF.',
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs) // phpcs:ignore
	{
		$className = $this->getClassShortName();

		$this->getExampleTemplate(__DIR__, $className)
			->renameClassName($className)
			->renameNamespace($assocArgs)
			->renameUse($assocArgs)
			->renameTextDomain($assocArgs)
			->outputWrite(static::OUTPUT_DIR, $className, $assocArgs);
	}
}
