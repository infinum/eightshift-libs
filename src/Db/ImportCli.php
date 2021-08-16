<?php

/**
 * Class that registers WPCLI command for Import.
 *
 * @package EightshiftLibs\Db
 */

declare(strict_types=1);

namespace EightshiftLibs\Db;

use EightshiftLibs\Cli\AbstractCli;
use WP_CLI\ExitException;

/**
 * Class ImportCli
 */
class ImportCli extends AbstractCli
{

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'run_import';
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
					'optional' => true,
				],
				[
					'type' => 'assoc',
					'name' => 'to',
					'description' => 'Set to what environment you want to import the data.',
					'optional' => true,
				],
			],
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs) // phpcs:ignore
	{
		require $this->getLibsPath('src/Db/DbImport.php');

		try {
			dbImport(
				$this->getProjectConfigRootPath(),
				[
					'from' => $assocArgs['from'] ?? '',
					'to' => $assocArgs['to'] ?? '',
				]
			);
		} catch (ExitException $e) {
			exit("{$e->getCode()}: {$e->getMessage()}"); // phpcs:ignore Eightshift.Security.CustomEscapeOutput.OutputNotEscaped
		}
	}
}
