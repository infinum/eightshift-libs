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
 * Class SetupCli
 */
class SetupCli extends AbstractCli
{

	/**
	 * Output dir relative path.
	 *
	 * @var string
	 */
	public const OUTPUT_DIR = '../../../';

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'init_setup';
	}

	/**
	 * Define default develop props.
	 *
	 * @param array $args WPCLI eval-file arguments.
	 *
	 * @return array
	 */
	public function getDevelopArgs(array $args): array
	{
		return [
			'root' => $args[1] ?? './',
		];
	}

	/**
	 * Get WPCLI command doc.
	 *
	 * @return array
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Initialize Command for automatic project setup and update.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'root',
					'description' => 'Define project root relative to initialization file of WP CLI.',
					'optional' => true,
				],
			],
		];
	}

	public function __invoke(array $args, array $assocArgs) // phpcs:ignore
	{
		// Get Props.
		$root = $assocArgs['root'] ?? static::OUTPUT_DIR;

		// Get setup.json file.
		try {
			$json = $this->getExampleTemplate(__DIR__, 'setup.json');
		} catch (ExitException $e) {
			exit("{$e->getCode()}: {$e->getMessage()}");
		}

		// Output json file to project root.
		try {
			$this->outputWrite($root, 'setup.json', $json, $assocArgs);
		} catch (ExitException $e) {
			exit("{$e->getCode()}: {$e->getMessage()}");
		}
	}
}
