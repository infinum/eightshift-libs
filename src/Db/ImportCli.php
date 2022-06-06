<?php

/**
 * Class that registers WPCLI command for Import.
 *
 * @package EightshiftLibs\Db
 */

declare(strict_types=1);

namespace EightshiftLibs\Db;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliRun;
use WP_CLI\ExitException;

/**
 * Class ImportCli
 */
class ImportCli extends AbstractCli
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
		return 'import';
	}

	/**
	 * Get WPCLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Run database import based on environments.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'from',
					'description' => 'Set from what environment you have exported the data.',
					'optional' => false,
				],
				[
					'type' => 'assoc',
					'name' => 'to',
					'description' => 'Set to what environment you want to import the data.',
					'optional' => false,
				],
			],
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Used as a project command to create database import based on the setup.json config file located in the project root.

				## EXAMPLES

				# Run db import command:
				$ wp boilerplate {$this->getCommandParentName()} {$this->getCommandName()} --from='production' --to='develop'

				## RESOURCES

				Command will be run using this code:
				https://github.com/infinum/eightshift-libs/blob/develop/src/Db/DbImport.php
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		require $this->getLibsPath('src/Db/DbImport.php');

		try {
			dbImport( // phpcs:ignore
				$this->getProjectConfigRootPath(),
				[
					'from' => $assocArgs['from'] ?? '',
					'to' => $assocArgs['to'] ?? '',
				]
			);
		} catch (ExitException $e) {
			exit("{$e->getCode()}: {$e->getMessage()}"); // phpcs:ignore Eightshift.Security.ComponentsEscape.OutputNotEscaped
		}
	}
}
