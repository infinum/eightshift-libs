<?php

/**
 * Class that registers WPCLI command for Export.
 *
 * @package EightshiftLibs\Db
 */

declare(strict_types=1);

namespace EightshiftLibs\Db;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliRun;
use WP_CLI\ExitException;

/**
 * Class ExportCli
 */
class ExportCli extends AbstractCli
{
	/**
	 * Get WPCLI command parent name
	 *
	 * @return string
	 */
	public function getCommandParentName(): string
	{
		return CliRun::COMMAND_NAME;
	}

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'export';
	}

	/**
	 * Get WPCLI command doc
	 *
	 * @return array<string, mixed>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Run database export with uploads folder.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'skip_db',
					'description' => 'If you want to skip exporting database.',
					'optional' => true,
					'options' => [
						'true',
						'false',
					],
				],
				[
					'type' => 'assoc',
					'name' => 'skip_uploads',
					'description' => 'If you want to skip exporting images.',
					'optional' => true,
				],
			],
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Used as a project command to create database export with zipped uploads folder in the root of your project.
				All configuration data is used from the setup.json file located in the project root.

				## EXAMPLES

				# Run db export command:
				$ wp boilerplate {$this->getCommandParentName()} {$this->getCommandName()}

				# Run db export command without uploads folder:
				$ wp boilerplate {$this->getCommandParentName()} {$this->getCommandName()} --skip_uploads='true'

				## RESOURCES

				Command will be run using this code:
				https://github.com/infinum/eightshift-libs/blob/develop/src/Db/DbExport.php
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		require $this->getLibsPath('src/Db/DbExport.php');

		try {
			dbExport( // phpcs:ignore
				$this->getProjectConfigRootPath(),
				[
					'skip_db' => $assocArgs['skip_db'] ?? false,
					'skip_uploads' => $assocArgs['skip_uploads'] ?? false,
				]
			);
		} catch (ExitException $e) {
			exit("{$e->getCode()}: {$e->getMessage()}"); // phpcs:ignore Eightshift.Security.ComponentsEscape.OutputNotEscaped
		}
	}
}
