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
 * Class SetupCli
 */
class SetupCli extends AbstractCli
{

	/**
	 * Output dir relative path.
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
					'type'        => 'assoc',
					'name'        => 'root',
					'description' => 'Define project root relative to initialization file of WP CLI.',
					'optional'    => true,
				],
			],
		];
	}

	/**
	 * Initializes setup.json
	 *
	 * @param array $args      Array of arguments form terminal.
	 * @param array $assocArgs Array of associative arguments form terminal.
	 */
	public function __invoke(array $args, array $assocArgs)
	{

		// Get Props.
		$root = $assocArgs['root'] ?? static::OUTPUT_DIR;

		// Get setup.json file.
		$json = $this->getExampleTemplate(__DIR__, 'setup.json');

		// Output json file to project root.
		$this->outputWrite($root, 'setup.json', $json);
	}
}
