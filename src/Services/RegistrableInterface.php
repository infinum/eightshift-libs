<?php

/**
 * File that holds the registrable interface.
 *
 * @package EightshiftLibs\Services
 */

declare(strict_types=1);

namespace EightshiftLibs\Services;

/**
 * Interface Registrable.
 *
 * An object that can be registered.
 */
interface RegistrableInterface
{

	/**
	 * Register the current registrable.
	 *
	 * A register method holds the plugin action and filter hooks.
	 * Following the single responsibility principle, every class
	 * holds a functionality for a certain part of the plugin.
	 * This is why every class should hold its own hooks.
	 *
	 * @return void
	 */
	public function register(): void;
}
