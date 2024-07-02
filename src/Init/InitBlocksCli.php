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
use EightshiftLibs\Blocks\UseVariationCli;
use EightshiftLibs\Blocks\UseWrapperCli;
use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliInit;
use EightshiftLibs\Helpers\Helpers;

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
		UseGlobalAssetsCli::class => [],
		UseWrapperCli::class => [],
		UseManifestCli::class => [],
		UseBlockCli::class => [
			'button',
			'card',
			'group',
			'heading',
			'image',
			'lists',
			'paragraph',
			'site-navigation',
			'site-footer',
		],
		UseComponentCli::class => [
			'admin-header-footer-picker',
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
			'paragraph',
			'tracking-before-body-end',
			'tracking-head',
			'social-networks',
		],
		UseVariationCli::class => [
			'card-simple'
		],
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
		$assocArgs = $this->prepareArgs($assocArgs);

		$this->getIntroText($assocArgs);

		$all = \filter_var($assocArgs['use_all'] ?? false, \FILTER_VALIDATE_BOOLEAN);

		$commands = static::COMMANDS;

		if ($all) {
			$commands = [];
			foreach (\array_keys(static::COMMANDS) as $command) {
				switch ($command) {
					case UseBlockCli::class:
						$commands[$command] = $this->getFolderItems($this->isTailwind() ? Helpers::getProjectPaths('blocksSourceTailwindCustom') : Helpers::getProjectPaths('blocksSourceCustom'));
						break;
					case UseComponentCli::class:
						$commands[$command] = $this->getFolderItems($this->isTailwind() ? Helpers::getProjectPaths('blocksSourceTailwindComponents') : Helpers::getProjectPaths('blocksSourceComponents'));
						break;
					case UseVariationCli::class:
						$commands[$command] = $this->getFolderItems($this->isTailwind() ? Helpers::getProjectPaths('blocksSourceTailwindVariations') : Helpers::getProjectPaths('blocksSourceVariations'));
						break;
					default:
						$commands[$command] = [];
						break;
				}
			}
		}

		$this->getInitBlocks($assocArgs, $commands);

		if (!$assocArgs[self::ARG_GROUP_OUTPUT]) {
			$this->cliLogAlert(
				'All the blocks have been created, you can start working on your awesome project!',
				'success',
				'Ready to go!'
			);
			$this->getAssetsCommandText();
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
			if ($items) {
				$this->runCliCommand(
					$className,
					$this->commandParentName,
					\array_merge(
						$assocArgs,
						[
							'name' => \implode(",", $items),
							'checkDependency' => false,
							self::ARG_GROUP_OUTPUT => true,
						]
					)
				);
			} else {
				$this->runCliCommand(
					$className,
					$this->commandParentName,
					\array_merge(
						$assocArgs,
						[
							'checkDependency' => false,
							self::ARG_GROUP_OUTPUT => true,
						]
					)
				);
			}
		}
	}
}
