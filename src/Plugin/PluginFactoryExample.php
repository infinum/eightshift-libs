<?php

/**
 * The file that defines a factory for activating / deactivating plugin.
 *
 * @package %g_namespace%
 */

declare(strict_types=1);

namespace %g_namespace%;

/**
 * The plugin factory class.
 */
class PluginFactoryExample
{
	/**
	 * Activate the plugin.
	 */
	public static function activate(): void
	{
		(new Activate())->activate();
	}

	/**
	 * Deactivate the plugin.
	 */
	public static function deactivate(): void
	{
		(new Deactivate())->deactivate();
	}
}
