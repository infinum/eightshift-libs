<?php

/**
 * The file that defines actions on plugin deactivation.
 *
 * @package %g_namespace%
 */

declare(strict_types=1);

namespace %g_namespace%;

use %g_use_libs%\Plugin\Plugin\HasDeactivationInterface;

/**
 * The plugin deactivation class.
 */
class DeactivateExample implements HasDeactivationInterface
{

	/**
	 * Deactivate the plugin.
	 */
	public function deactivate(): void
	{
		\flush_rewrite_rules();
	}
}
