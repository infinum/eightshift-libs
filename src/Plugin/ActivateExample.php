<?php

/**
 * The file that defines actions on plugin activation.
 *
 * @package %g_namespace%
 */

declare(strict_types=1);

namespace %g_namespace%;

use %g_use_libs%\Plugin\HasActivationInterface;

/**
 * The plugin activation class.
 */
class ActivateExample implements HasActivationInterface
{

	/**
	 * Activate the plugin.
	 *
	 * @since 1.0.0
	 */
	public function activate(): void
	{
		\flush_rewrite_rules();
	}
}
