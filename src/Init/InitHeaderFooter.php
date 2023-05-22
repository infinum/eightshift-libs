<?php

/**
 * Class that registers WP-CLI command for the initialization of reusable block header and footer.
 *
 * @package EightshiftLibs\Cli
 */

declare(strict_types=1);

namespace EightshiftLibs\Init;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliInit;

/**
 * Class InitHeaderFooter
 */
class InitHeaderFooter extends AbstractCli
{
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
		return 'header-footer';
	}

	/**
	 * Get WP-CLI command doc.
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Add a default header/footer reusable block.',
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Generates a 'Header' and 'Footer' reusable blocks, filled with the appropriate blocks.
				Also, the blocks are automatically pre-selected as the main header/footer partials.

				## EXAMPLES

				# Setup theme:
				$ wp {$this->commandParentName} {$this->getCommandParentName()} {$this->getCommandName()}
			"),
		];
	}

	// @phpstan-ignore-next-line
	public function __invoke(array $args, array $assocArgs)
	{
		$groupOutput = $assocArgs['groupOutput'] ?? false;

		if (!$groupOutput) {
			$this->getIntroText();
		}

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
			\WP_CLI::runcommand("option update es-header-partial {$headerReusableBlockId}", $createBlocksOptions);
		}

		if (!$groupOutput) {
			$this->cliLogAlert('Header and footer reusable blocks added');
		}
	}
}
