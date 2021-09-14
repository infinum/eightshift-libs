<?php

/**
 * Class that registers WPCLI command for ReadmeCli.
 *
 * @package EightshiftLibs\Readme
 */

declare(strict_types=1);

namespace EightshiftLibs\Readme;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class ReadmeCli
 */
class ReadmeCli extends AbstractCli
{

	/**
	 * Output dir relative path.
	 *
	 * @var string
	 */
	public const OUTPUT_DIR = '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'init_readme';
	}

	/**
	 * Define default develop props.
	 *
	 * @param string[] $args WPCLI eval-file arguments.
	 *
	 * @return array<string, mixed>
	 */
	public function getDevelopArgs(array $args): array
	{
		return [
			'root' => $args[1] ?? './',
		];
	}

	/**
	 * Get WPCLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Initialize Command for building your projects readme.',
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

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs) // phpcs:ignore
	{
		// Get Props.
		$root = $assocArgs['root'] ?? static::OUTPUT_DIR;

		// Read the template contents, and replace the placeholders with provided variables.
		$this->getExampleTemplate(__DIR__, 'README.md')
			->outputWrite($root, 'README.md', $assocArgs);
	}
}
