<?php

/**
 * Class that registers WPCLI command for Blocks Block.
 *
 * @package EightshiftLibs\Blocks
 */

declare(strict_types=1);

namespace EightshiftLibs\Blocks;

/**
 * Class BlockCli
 */
class BlockCli extends AbstractBlocksCli
{
	/**
	 * CLI command name
	 *
	 * @var string
	 */
	public const COMMAND_NAME = 'use_block';

	/**
	 * Output dir relative path
	 *
	 * @var string
	 */
	public const OUTPUT_DIR = 'src' . \DIRECTORY_SEPARATOR . 'Blocks' . \DIRECTORY_SEPARATOR . 'custom';

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
	 * Define default develop props.
	 *
	 * @param string[] $args WPCLI eval-file arguments.
	 *
	 * @return array<string, mixed>
	 */
	public function getDevelopArgs(array $args): array
	{
		return [
			'name' => $args[1] ?? 'button',
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
			'shortdesc' => 'Copy Block from library to your project.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'name',
					'description' => 'Specify block name.',
					'optional' => \defined('ES_DEVELOP_MODE') ? ES_DEVELOP_MODE : false
				],
			],
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs) // phpcs:ignore Eightshift.Commenting.FunctionComment.WrongStyle
	{
		$this->blocksMove($assocArgs, static::OUTPUT_DIR);
	}
}
