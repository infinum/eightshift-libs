<?php

/**
 * Class that registers WPCLI command for Setup.
 *
 * @package EightshiftLibs\Setup
 */

declare(strict_types=1);

namespace EightshiftLibs\Setup;

use EightshiftLibs\Cli\AbstractCli;
use WP_CLI\ExitException;

/**
 * Class UpdateCli
 */
class UpdateCli extends AbstractCli
{
	public const COMMAND_NAME = 'run_update';

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
	 * Get WPCLI command doc.
	 *
	 * @return array
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Run project update with details stored in setup.json file.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'skip_core',
					'description' => 'If you want to skip core update/installation, provide bool on this attr.',
					'optional' => true,
				],
				[
					'type' => 'assoc',
					'name' => 'skip_plugins',
					'description' => 'If you want to skip all plugins update/installation, provide bool on this attr.',
					'optional' => true,
				],
				[
					'type' => 'assoc',
					'name' => 'skip_plugins_core',
					'description' => 'If you want to skip plugins only from core update/installation, provide bool on this attr.',
					'optional' => true,
				],
				[
					'type' => 'assoc',
					'name' => 'skip_plugins_github',
					'description' => 'If you want to skip plugins only from github update/installation, provide bool on this attr.',
					'optional' => true,
				],
				[
					'type' => 'assoc',
					'name' => 'skip_themes',
					'description' => 'If you want to skip themes update/installation, provide bool on this attr.',
					'optional' => true,
				],
			],
		];
	}

	public function __invoke(array $args, array $assocArgs) // phpcs:ignore
	{
		require_once $this->getLibsPath('src/Setup/Setup.php');

		$setupFilename = 'setup.json';

		if (getenv('TEST') !== false) {
			$setupFilename = $this->getProjectConfigRootPath() . '/cliOutput/setup.json';
		}

		try {
			setup(
				$this->getProjectConfigRootPath(),
				[
					'skip_core' => $assocArgs['skip_core'] ?? false,
					'skip_plugins' => $assocArgs['skip_plugins'] ?? false,
					'skip_plugins_core' => $assocArgs['skip_plugins_core'] ?? false,
					'skip_plugins_github' => $assocArgs['skip_plugins_github'] ?? false,
					'skip_themes' => $assocArgs['skip_themes'] ?? false,
				],
				$setupFilename
			);
		} catch (ExitException $e) {
			exit("{$e->getCode()}: {$e->getMessage()}");
		}
	}
}
