<?php

/**
 * Class that registers WP-CLI command interface used in all CLI commands.
 *
 * @package EightshiftLibs\Cli
 */

declare(strict_types=1);

namespace EightshiftLibs\Cli;

interface CliInterface
{
	/**
	 * Register method for WP-CLI command
	 *
	 * @return void
	 */
	public function register(): void;

	/**
	 * Make every class implementing this interface invokable.
	 *
	 * This is done because we are using WP-CLI commands to do our bidding.
	 *
	 * @param array $args      Array of arguments form terminal.
	 * @param array $assocArgs Array of arguments form terminal associative.
	 *
	 * @return void
	 */
	public function __invoke(array $args, array $assocArgs); // @phpstan-ignore-line

	/**
	 * Get WP-CLI command parent name
	 *
	 * @return string
	 */
	public function getCommandParentName(): string;

	/**
	 * Get WP-CLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string;

	/**
	 * Get WP-CLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array;
}
