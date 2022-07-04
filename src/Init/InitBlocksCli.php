<?php

/**
 * Class that registers WPCLI command for Blocks init.
 *
 * @package EightshiftLibs\Init
 */

declare(strict_types=1);

namespace EightshiftLibs\Init;

use EightshiftLibs\Blocks\BlocksCli;
use EightshiftLibs\Blocks\UseAssetsCli;
use EightshiftLibs\Blocks\UseBlockCli;
use EightshiftLibs\Blocks\UseComponentCli;
use EightshiftLibs\Blocks\UseGlobalAssetsCli;
use EightshiftLibs\Blocks\UseManifestCli;
use EightshiftLibs\Blocks\UseStorybookCli;
use EightshiftLibs\Blocks\UseVariationCli;
use EightshiftLibs\Blocks\UseWrapperCli;
use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliInit;
use ReflectionClass;

/**
 * Class InitBlocksCli
 */
class InitBlocksCli extends AbstractCli
{
	/**
	 * All commands to run on init for the 
	 */
	public const INIT = [
		BlocksCli::class => [],
		UseAssetsCli::class => [],
		UseStorybookCli::class => [],
		UseGlobalAssetsCli::class => [],
		UseWrapperCli::class => [],
		UseManifestCli::class => [],
		UseBlockCli::class => [
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
		UseComponentCli::class => [
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
				'button-false',
				'heading',
				'layout',
				'responsiveVariables',
				'typography',
				'variables',
			],
		],
		UseVariationCli::class => [
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
		return CliInit::COMMAND_NAME;
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

				$class->__invoke([], ['name' =>  implode(",", $innerItems)]);
			} else {
				$class->__invoke([], []);
			}
		}
	}
}
