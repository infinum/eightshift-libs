<?php

/**
 * Class that registers WPCLI command for Export.
 *
 * @package EightshiftLibs\Db
 */

declare(strict_types=1);

namespace EightshiftLibs\Db;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class ExportCli
 */
class ExportCli extends AbstractCli
{
	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'run_export';
	}

	/**
	 * Get WPCLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Run database export with images.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'skip_db',
					'description' => 'If you want to skip exporting database.',
					'optional' => true,
				],
				[
					'type' => 'assoc',
					'name' => 'skip_uploads',
					'description' => 'If you want to skip exporting images.',
					'optional' => true,
				],
			],
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs) // phpcs:ignore
	{
		require $this->getLibsPath('src/Db/DbExport.php');

		dbExport(
			$this->getProjectConfigRootPath(),
			[
				'skip_db' => $assocArgs['skip_db'] ?? false,
				'skip_uploads' => $assocArgs['skip_uploads'] ?? false,
			]
		);
	}
}
