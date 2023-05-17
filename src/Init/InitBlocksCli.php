<?php

/**
 * Class that registers WP-CLI command for Blocks init.
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
use EightshiftLibs\Helpers\Components;
use ReflectionClass;

/**
 * Class InitBlocksCli
 */
class InitBlocksCli extends AbstractCli
{
	/**
	 * All commands to run on init.
	 */
	public const COMMANDS = [
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
				'site-header',
				'site-footer',
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
				'card-simple'
			],
			'test' => [
				'card-simple'
			]
		]
	];

	/**
	 * Get WP-CLI command parent name.
	 *
	 * @return string
	 */
	public function getCommandParentName(): string
	{
		return CliInit::COMMAND_NAME;
	}

	/**
	 * Get WP-CLI command name.
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'blocks';
	}

	/**
	 * Get WP-CLI command doc.
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Create all files for blocks to work.',
			'synopsis' => [
				[
					'type' => 'flag',
					'name' => 'use_all',
					'description' => 'Output all items to your project.',
					'optional' => true,
					'default' => false,
				],
			],
			'longdesc' => $this->prepareLongDesc("
				This command will copy all initial blocks, components, manifests and service classes to you project in order to start using block editor.

				## EXAMPLES
				$ wp {$this->commandParentName} create blocks_init
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		$groupOutput = $assocArgs['groupOutput'] ?? false;
		$all = $assocArgs['use_all'] ?? false;

		if (!$groupOutput) {
			$this->getIntroText();
		}

		$this->cliLog('Creating block editor files:', 'C');

		$commands = static::COMMANDS;

		if ($all) {
			$commands = [];
			$type = \getenv('ES_TEST') ? 'test' : 'default';

			foreach (\array_keys(static::COMMANDS) as $command) {
				switch ($command) {
					case UseBlockCli::class:
						$commands[$command][$type] = $this->getFolderItems(Components::getProjectPaths('blocksSourceCustom'));
						break;
					case UseComponentCli::class:
						$commands[$command][$type] = $this->getFolderItems(Components::getProjectPaths('blocksSourceComponents'));
						break;
					case UseVariationCli::class:
						$commands[$command][$type] = $this->getFolderItems(Components::getProjectPaths('blocksSourceVariations'));
						break;
					default:
						$commands[$command] = [];
						break;
				}
			}
		}

		$this->getInitBlocks($assocArgs, $commands);

		if (!$groupOutput) {
			$this->cliLog('--------------------------------------------------');
			$this->cliLog('We have moved everything you need to start creating WordPress blocks. Please type `npm start` in your terminal to kickstart your assets bundle process.', "M");
			$this->cliLog('Happy developing!', "M");
		}
	}

	/**
	 * Init block by providing list of commands.
	 *
	 * @param array<string, mixed> $assocArgs List of arguments for options.
	 * @param array<string, mixed> $commands Commands to use.
	 *
	 * @return void
	 */
	private function getInitBlocks(array $assocArgs, array $commands): void
	{
		foreach ($commands as $className => $items) {
			$reflectionClass = new ReflectionClass($className);
			$class = $reflectionClass->newInstanceArgs([$this->commandParentName]);

			if ($items) {
				$innerItems = $items['default'];

				if (\getenv('ES_TEST')) {
					$innerItems = $items['test'];
				}

				$class->__invoke([], \array_merge(
					$assocArgs,
					[
						'name' => \implode(",", $innerItems),
						'groupOutput' => true,
						'introOutput' => false,
						'checkDependency' => false,
					]
				));
			} else {
				$class->__invoke([], \array_merge(
					$assocArgs,
					[
						'groupOutput' => true,
						'introOutput' => false,
						'checkDependency' => false,
					]
				));
			}
		}
	}
}
