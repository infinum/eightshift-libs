<?php

/**
 * The WpCliExample specific functionality.
 *
 * @package EightshiftLibs\WpCli
 */

declare(strict_types=1);

namespace EightshiftBoilerplate\WpCli;

use EightshiftLibs\Helpers\Helpers;
use EightshiftLibs\Services\ServiceCliInterface;
use WP_CLI;

/**
 * Class WpCliExample
 */
class WpCliExample implements ServiceCliInterface
{
	/**
	 * CLI command name
	 *
	 * @var string
	 */
	public const COMMAND_NAME = '%command_name%';

	/**
	 * Register method for WPCLI command
	 *
	 * @return void
	 */
	public function register(): void
	{
		\add_action('cli_init', [$this, 'registerCommand']);
	}

	/**
	 * Register actial command in WP-CLI.
	 *
	 * @return void
	 */
	public function registerCommand(): void
	{
		WP_CLI::add_command(
			Helpers::getThemeName() . ' ' . self::COMMAND_NAME,
			\get_class($this),
			$this->getDocs()
		);
	}

	/**
	 * Get WPCLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDocs(): array
	{
		return [
			'shortdesc' => 'Generates custom WPCLI command in your project.'
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs) // phpcs:ignore Eightshift.Commenting.FunctionComment.WrongStyle
	{
		// Place your logic here.
	}
}
