<?php

/**
 * Class that registers WP-CLI command initial setup of theme project.
 *
 * @package EightshiftLibs\Cli
 */

declare(strict_types=1);

namespace EightshiftLibs\Init;

use EightshiftLibs\AdminMenus\AdminReusableBlocksMenuCli;
use EightshiftLibs\AdminMenus\ReusableBlocksHeaderFooterCli;
use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliInit;
use EightshiftLibs\Config\ConfigCli;
use EightshiftLibs\Enqueue\Admin\EnqueueAdminCli;
use EightshiftLibs\Enqueue\Blocks\EnqueueBlocksCli;
use EightshiftLibs\Enqueue\Theme\EnqueueThemeCli;
use EightshiftLibs\Main\MainCli;
use EightshiftLibs\Manifest\ManifestCli;
use ReflectionClass;

/**
 * Class InitThemeCli
 */
class InitThemeCli extends AbstractCli
{
	/**
	 * All classes for initial theme setup for project.
	 *
	 * @var array<int, mixed>
	 */
	public const COMMANDS = [
		[
			'type' => 'sc',
			'label' => 'Setting service classes:',
			'items' => [
				ConfigCli::class,
				MainCli::class,
				ManifestCli::class,
				EnqueueAdminCli::class,
				EnqueueBlocksCli::class,
				EnqueueThemeCli::class,
				AdminReusableBlocksMenuCli::class,
				ReusableBlocksHeaderFooterCli::class,
			],
		],
		[
			'type' => 'blocks',
			'label' => '',
			'items' => [
				InitBlocksCli::class,
			],
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
		return 'theme';
	}

	/**
	 * Get WP-CLI command doc.
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Kickstart your WordPress theme with this simple command.',
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Generates initial theme setup with all files to create a custom theme.

				## EXAMPLES

				# Setup theme:
				$ wp {$this->commandParentName} {$this->getCommandParentName()} {$this->getCommandName()}
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		$groupOutput = $assocArgs['groupOutput'] ?? false;

		if (!$groupOutput) {
			$this->getIntroText();
		}

		foreach (static::COMMANDS as $item) {
			$label = $item['label'] ?? '';
			$items = $item['items'] ?? [];
			$type = $item['type'] ?? '';

			if ($label) {
				$this->cliLog($label, 'C');
			}

			if ($items) {
				foreach ($items as $className) {
					$reflectionClass = new ReflectionClass($className);
					$class = $reflectionClass->newInstanceArgs([$this->commandParentName]);

					$class->__invoke([], \array_merge(
						$assocArgs,
						[
							'groupOutput' => $type === 'blocks',
							'introOutput' => false,
						]
					));
				}
			}

			$this->cliLog('--------------------------------------------------');
		}

		// NPM and Composer install.
		$c1Out = \WP_CLI::launch('npm install');
		$this->cliLog("{$c1Out}");
		$c2Out = \WP_CLI::launch('composer install');
		$this->cliLog("{$c2Out}");
		$c3Out = \WP_CLI::launch('npm run build');
		$this->cliLog("{$c3Out}");

		// Add initial posts.
		$createBlocksOptions = [ 'return' => true ];

		$createFooterCommand = 'post create --post_title="Footer" --post_type="wp_block" --post_status="publish" --porcelain --post_content=\'<!-- wp:eightshift-boilerplate/site-footer {"siteFooterLogoUrl":"/wp-content/themes/t24v7/public/logo.svg","siteFooterCopyrightContent":"Copyright 2023, Eightshift DevKit","siteFooterLinks":[{"id":"5f573082-2354-4b95-baef-0dd9abbe73fd","url":"#","text":"Link","newTab":false}]} /-->\'';
		$footerReusableBlockId = \WP_CLI::runcommand($createFooterCommand, $createBlocksOptions);

		if (!empty($footerReusableBlockId)) {
			\WP_CLI::runcommand("option update es-footer-partial {$footerReusableBlockId}", $createBlocksOptions);
		}

		$createHeaderCommand = 'post create --post_title="Header" --post_type="wp_block" --post_status="publish" --porcelain --post_content=\'<!-- wp:eightshift-boilerplate/site-navigation {"siteNavigationLogoUrl":"https://themedev.test/wp-content/uploads/2023/05/es.png","siteNavigationLinks":[{"id":"9e7d54c8-4271-4b23-9b3b-e163e4bac92b","url":"#","text":"Welcome","newTab":false}]} /-->\'';
		$headerReusableBlockId = \WP_CLI::runcommand($createHeaderCommand, $createBlocksOptions);

		if (!empty($headerReusableBlockId)) {
			\WP_CLI::runcommand("option update es-header-partial {$footerReusableBlockId}", $createBlocksOptions);
		}

		if (!$groupOutput) {
			$this->cliLog('We have moved everything you need to start creating your awesome WordPress theme. Please type `npm start` in your terminal to kickstart your assets bundle process.', "M");
			$this->cliLog('Happy developing!', "M");
		}
	}
}
