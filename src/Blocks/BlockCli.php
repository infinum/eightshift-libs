<?php

/**
 * Class that registers WPCLI command for Blocks Block.
 *
 * @package EightshiftLibs\Blocks
 */

declare(strict_types=1);

namespace EightshiftLibs\Blocks;

use EightshiftLibs\Cli\AbstractCli;
use WP_CLI\ExitException;

/**
 * Class BlockCli
 */
class BlockCli extends AbstractCli
{

	/**
	 * Output dir relative path
	 *
	 * @var string
	 */
	public const OUTPUT_DIR = 'src/Blocks';

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'use_block';
	}

	/**
	 * Get WPCLI command doc
	 *
	 * @return array
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Copy Block from library to your project.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'name',
					'description' => 'Specify block name.',
					'optional' => false,
				],
			],
		];
	}

	public function __invoke(array $args, array $assocArgs) // phpcs:ignore
	{
		// Get Props.
		$name = $assocArgs['name'] ?? '';

		$root = $this->getProjectRootPath();
		$rootNode = $this->getFrontendLibsBlockPath();

		$sourcePath = "{$rootNode}/src/Blocks/custom/{$name}";
		$destinationPath = "{$root}/src/Blocks/custom/{$name}";

		// Source doesn't exist.
		if (!file_exists($sourcePath)) {
			\WP_CLI::error(
				sprintf(
					'The block "%s" doesn\'t exist in our library. Please check the docs for all available blocks.',
					$sourcePath
				)
			);
		}

		// Destination exists.
		if (file_exists($destinationPath)) {
			\WP_CLI::error(
				sprintf(
					'The block in you project exists on this "%s" path. Please check or remove that folder before running this command again.',
					$destinationPath
				)
			);
		}

		system("cp -R {$sourcePath}/. {$destinationPath}/");

		\WP_CLI::success('Block successfully created.');
	}
}
