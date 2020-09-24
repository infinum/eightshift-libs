<?php

/**
 * Class that registers WPCLI command for Setup.
 *
 * @package EightshiftLibs\Setup
 */

declare(strict_types=1);

namespace EightshiftLibs\Setup;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class UpdateCli
 */
class UpdateCli extends AbstractCli
{

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'run_update';
	}

	/**
	 * Get WPCLI command doc.
	 *
	 * @return array
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Run project update with detailes stored in setup.json file.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'skip_core',
					'description' => 'If you want to skip core update/instalation provide bool on this attr.',
					'optional' => true,
				],
				[
					'type' => 'assoc',
					'name' => 'skip_plugins',
					'description' => 'If you want to skip all plugins update/instalation provide bool on this attr.',
					'optional' => true,
				],
				[
					'type' => 'assoc',
					'name' => 'skip_plugins_core',
					'description' => 'If you want to skip plugins only from core update/instalation provide bool on this attr.',
					'optional' => true,
				],
				[
					'type' => 'assoc',
					'name' => 'skip_plugins_github',
					'description' => 'If you want to skip plugins only from github update/instalation provide bool on this attr.',
					'optional' => true,
				],
				[
					'type' => 'assoc',
					'name' => 'skip_themes',
					'description' => 'If you want to skip themes update/instalation provide bool on this attr.',
					'optional' => true,
				],
			],
		];
	}

	public function __invoke(array $args, array $assocArgs) // phpcs:ignore
	{
		require $this->getLibsPath('src/Setup/Setup.php');

		setup(
			$this->getProjectConfigRootPath(),
			[
				'skip_core' => $assocArgs['skip_core'] ?? false,
				'skip_plugins' => $assocArgs['skip_plugins'] ?? false,
				'skip_plugins_core' => $assocArgs['skip_plugins_core'] ?? false,
				'skip_plugins_github' => $assocArgs['skip_plugins_github'] ?? false,
				'skip_themes' => $assocArgs['skip_themes'] ?? false,
			]
		);
	}
}
