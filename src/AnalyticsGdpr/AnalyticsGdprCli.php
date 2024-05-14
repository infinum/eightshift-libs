<?php

/**
 * Class that registers WPCLI command for AnalyticsGdpr using ACF.
 *
 * @package EightshiftLibs\AnalyticsGdpr
 */

declare(strict_types=1);

namespace EightshiftLibs\AnalyticsGdpr;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliCreate;
use EightshiftLibs\Helpers\Helpers;

/**
 * Class AnalyticsGdprCli
 */
class AnalyticsGdprCli extends AbstractCli
{
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
		return 'analytics-gdpr-settings';
	}

	/**
	 * Get WPCLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Create project Analytics and GDPR Settings classes using ACF plugin.',
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Used to create Advanced Custom Fields analytics & GDPR settings service class to register project specific options.
				ACF plugin must be installed.

				## EXAMPLES

				# Create service class:
				$ wp {$this->commandParentName} {$this->getCommandParentName()} {$this->getCommandName()}

				## RESOURCES

				Service class will be created from this example:
				https://github.com/infinum/eightshift-libs/blob/develop/src/AnalyticsGdpr/AnalyticsGdprExample.php

				ACF documentation:
				https://www.advancedcustomfields.com/resources/options-page/
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		$assocArgs = $this->prepareArgs($assocArgs);
		$this->getIntroText();

		$className = $this->getClassShortName();

		$this->getExampleTemplate(__DIR__, $className)
			->renameClassName($className)
			->renameGlobals($assocArgs)
			->outputWrite(Helpers::getProjectPaths('srcDestination', 'AnalyticsGdpr'), "{$className}.php", $assocArgs);
	}
}
