<?php

/**
 * Class that registers WPCLI command interface used in all CLI commands.
 *
 * @package EightshiftLibs\Cli
 */

declare(strict_types=1);

namespace EightshiftLibs\Cli;

interface CliInterface
{

	/**
	 * Register method for WPCLI command
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
	public function __invoke(array $args, array $assocArgs);

	/**
	 * Method that creates actual WPCLI command in terminal.
	 *
	 * @return void
	 */
	public function registerCommand(): void;

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string;

	/**
	 * Get WPCLI trigger class name.
	 *
	 * @return string
	 */
	public function getClassName(): string;

	/**
	 * Get WPCLI command doc.
	 *
	 * @return string
	 */
	public function getDoc(): array;
}
