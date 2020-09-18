<?php

/**
 * Class that registers WPCLI command for Import.
 *
 * @package EightshiftLibs\Db
 */

declare(strict_types=1);

namespace EightshiftLibs\Db;

use EightshiftLibs\Cli\AbstractCli;

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
	 * Get WPCLI command doc.
	 *
	 * @return array
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Run database import based on enviroments.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'from',
					'description' => 'Set from what enviroment you have exported the data.',
					'optional' => true,
				],
				[
					'type' => 'assoc',
					'name' => 'to',
					'description' => 'Set to what enviroment you want to import the data.',
					'optional' => true,
				],
			],
		];
	}

	public function __invoke(array $args, array $assocArgs) // phpcs:ignore
	{
		require $this->getLibsPath('src/Db/DbImport.php');

		dbImport(
			$this->getProjectConfigRootPath(),
			[
				'from' => $assocArgs['from'] ?? '',
				'to' => $assocArgs['to'] ?? '',
			]
		);
	}
}
