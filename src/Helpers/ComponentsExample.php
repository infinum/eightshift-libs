<?php

/**
 * Helpers for components
 *
 * @package EightshiftBoilerplate\Helpers
 */

declare(strict_types=1);

namespace EightshiftBoilerplate\Helpers;

use EightshiftLibs\Helpers\Components as ComponentsLibs;
use EightshiftBoilerplate\Blocks\Blocks;

/**
 * Helpers for components
 */
class ComponentsExample extends ComponentsLibs
{
	/**
	 * Return project details from global window object.
	 *
	 * @return array<mixed>
	 */
	public static function getSettings(): array
	{
		return \apply_filters(Blocks::BLOCKS_DEPENDENCY_FILTER_NAME, []);
	}
}
