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
		return 'blocks';
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
		foreach (static::COMMANDS as $className => $items) {
			$reflectionClass = new ReflectionClass($className);
			$class = $reflectionClass->newInstanceArgs([$this->commandParentName]);

			if ($items) {
				$innerItems = $items['default'];

				if (\getenv('ES_TEST')) {
					$innerItems = $items['test'];
				}

				$class->__invoke([], [
					'name' =>  implode(",", $innerItems),
					 array_merge([
						$assocArgs,
						[
							'groupOutput' => true,
						]
					])
				]);
			} else {
				$class->__invoke([], [
					 array_merge([
						$assocArgs,
						[
							'groupOutput' => true,
						]
					])
				]);
			}
		}

		$this->cliLog('--------------------------------------------------');

		$this->cliLog('We have moved everything you need to start creating WordPress blocks. Please type `npm start` in your terminal to kickstart your assets bundle process.', "C");
		$this->cliLog('Happy developing!', "C");
	}
}
