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
			'admin-header-footer-picker',
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
		$groupOutput = $assocArgs[self::ARG_GROUP_OUTPUT];

		$this->getIntroText($assocArgs);

		if (!$groupOutput) {
			$this->cliLog("%w╭\n│ %nCreating block editor files", 'mixed');
		}

		$this->getInitBlocks($assocArgs, static::COMMANDS);

		if (!$groupOutput) {
			$this->cliLog('╰', 'w');
			$this->cliLogAlert('Files copied! Run `npm start` to build all the assets.\n\nHappy developing!', 'success', \__('Ready to go!', 'eightshift-libs'));
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
						]
					)
				);
			}
		}
	}
}
