<?php

/**
 * Class that registers WPCLI command for Blocks init.
 *
 * @package EightshiftLibs\Blocks
 */

declare(strict_types=1);

namespace EightshiftLibs\Blocks;

use EightshiftLibs\BlockPatterns\BlockPatternCli;
use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliCreate;
use EightshiftLibs\Cli\ParentGroups\CliBlocks;
use EightshiftLibs\Helpers\Components;
use ReflectionClass;
use WP_CLI;

/**
 * Class BlocksInitCli
 */
class BlocksInitCli extends AbstractCli
{
	/**
	 * All commands to run on init for the 
	 */
	public const INIT = [
		BlocksCli::class => [],
		BlocksAssetsCli::class => [],
		// BlockPatternCli::class => [],
		BlocksStorybookCli::class => [],
		BlocksGlobalAssetsCli::class => [],
		BlockWrapperCli::class => [],
		BlocksManifestCli::class => [],
		BlockCli::class => [
			'default' => [
				'button',
				'card',
				'group',
				'heading',
				'image',
				'lists',
				'paragraph',
			],
			'test' => [
				'button',
				'heading',
			],
		],
		BlockComponentCli::class => [
			'default' => [
				'button',
				'card',
				'copyright',
				'drawer',
				'footer',
				'hamburger',
				'head',
				'header',
				'heading',
				'icon',
				'image',
				'layout-three-columns',
				'lists',
				'logo',
				'menu',
				'paragraph',
				'tracking-before-body-end',
				'tracking-head',
			],
			'test' => [
				'button',
				'heading',
				'layout',
				'typography',
			],
		],
		BlockVariationCli::class => [
			'default' => [
				'button-block',
			],
			'test' => [
				'button-block',
			],
		],
	];

	/**
	 * Get WPCLI command parent name
	 *
	 * @return string
	 */
	public function getCommandParentName(): string
	{
		return CliCreate::COMMAND_NAME;
	}

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'blocks_init';
	}

	/**
	 * Get WPCLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Create all files for blocks to work.',
			'longdesc' => "
				This command will copy all initial block, componens, manifests na service classes to you project in order to start using block editor.

				## EXAMPLES
				$ wp boilerplate create blocks_init
			",
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		foreach (static::INIT as $className => $items) {
			$reflectionClass = new ReflectionClass($className);
			$class = $reflectionClass->newInstanceArgs([$this->commandParentName]);

			if ($items) {
				$innerItems = $items['default'];

				if (\getenv('ES_TEST')) {
					$innerItems = $items['test'];
				}

				foreach ($innerItems as $item) {
					$class->__invoke([], ['name' => $item]);
				}
			} else {
				$class->__invoke([], []);
			}
		}
	}
}
