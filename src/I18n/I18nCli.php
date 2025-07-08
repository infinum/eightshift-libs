<?php

/**
 * Class that registers WP-CLI command for I18n.
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
	 * Get WP-CLI command parent name
	 *
	 * @return string
	 */
	public function getCommandParentName(): string
	{
		return CliCreate::COMMAND_NAME;
	}

	/**
	 * Get WP-CLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'i18n';
	}

	/**
	 * Define default arguments.
	 *
	 * @return array<string, int|string|boolean>
	 */
	public function getDefaultArgs(): array
	{
		return [
			'type' => 'theme',
		];
	}

	/**
	 * Get WP-CLI command doc
	 *
	 * @return array<string, mixed>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Create i18n language service class.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'type',
					'description' => 'Type of the i18n service class. Available options: theme, plugin.',
					'optional' => false,
					'default' => $this->getDefaultArg('type'),
					'options' => [
						'theme',
						'plugin',
					],
				],
			],
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Used to create language service class to register custom languages to your theme or plugin.

				## EXAMPLES

				# Create service class:
				$ wp {$this->commandParentName} {$this->getCommandParentName()} {$this->getCommandName()} --type='theme'

				## RESOURCES

				Service class will be created from this example:
				https://github.com/infinum/eightshift-libs/blob/develop/src/I18n/I18nThemeExample.php
				https://github.com/infinum/eightshift-libs/blob/develop/src/I18n/I18nPluginExample.php
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		$assocArgs = $this->prepareArgs($assocArgs);

		$this->getIntroText($assocArgs);

		// Get Props.
		$type = $this->getArg($assocArgs, 'type');

		$sep = \DIRECTORY_SEPARATOR;

		$sourceLanguages = Helpers::getProjectPaths('src', "I18n{$sep}languages");

		if (!\is_dir($sourceLanguages)) {
			\mkdir($sourceLanguages, 0755, true);
		}

		// Read the template contents, and replace the placeholders with provided variables.
		$this->getExampleTemplate(__DIR__, $type === 'theme' ? 'I18nTheme' : 'I18nPlugin')
			->searchReplaceString($type === 'theme' ? 'I18nThemeExample' : 'I18nPluginExample', 'I18n')
			->renameGlobals($assocArgs)
			->outputWrite(Helpers::getProjectPaths('src', 'I18n'), "I18n.php", $assocArgs);
	}
}
