<?php

/**
 * Class that registers WPCLI command for I18n.
 *
 * @package EightshiftLibs\I18n
 */

declare(strict_types=1);

namespace EightshiftLibs\I18n;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliCreate;
use EightshiftLibs\Helpers\Helpers;

/**
 * Class I18nCli
 */
class I18nCli extends AbstractCli
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
		return 'i18n';
	}

	/**
	 * Get WPCLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Create i18n language service class.',
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Used to create language service class to register custom languages to your theme or plugin.

				## EXAMPLES

				# Create service class:
				$ wp {$this->commandParentName} {$this->getCommandParentName()} {$this->getCommandName()}

				## RESOURCES

				Service class will be created from this example:
				https://github.com/infinum/eightshift-libs/blob/develop/src/I18n/I18nExample.php
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		$assocArgs = $this->prepareArgs($assocArgs);

		$this->getIntroText();

		$className = $this->getClassShortName();

		$sep = \DIRECTORY_SEPARATOR;

		$sourceLanguages = Helpers::getProjectPaths('srcDestination', "I18n{$sep}languages");

		if (!\is_dir($sourceLanguages)) {
			\mkdir($sourceLanguages, 0755, true);
		}

		// Read the template contents, and replace the placeholders with provided variables.
		$this->getExampleTemplate(__DIR__, $className)
			->renameClassName($className)
			->renameGlobals($assocArgs)
			->outputWrite(Helpers::getProjectPaths('srcDestination', 'I18n'), "{$className}.php", $assocArgs);
	}
}
