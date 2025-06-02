<?php

/**
 * All the helpers for components.
 *
 * @package EightshiftLibs\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Helpers;

/**
 * All the helpers for components.
 */
class Helpers
{
	/**
	 * Cache trait.
	 */
	use CacheTrait;

	/**
	 * Store blocks trait.
	 */
	use StoreBlocksTrait;

	/**
	 * Css Variables trait.
	 */
	use CssVariablesTrait;

	/**
	 * Render trait.
	 */
	use RenderTrait;

	/**
	 * Paths trait.
	 */
	use PathsTrait;

	/**
	 * Selectors trait.
	 */
	use SelectorsTrait;

	/**
	 * Attributes trait.
	 */
	use AttributesTrait;

	/**
	 * Generic general helper trait.
	 */
	use GeneralTrait;

	/**
	 * Shortcode trait.
	 */
	use ShortcodeTrait;

	/**
	 * Post trait.
	 */
	use PostTrait;

	/**
	 * Media trait.
	 */
	use MediaTrait;

	/**
	 * API trait.
	 */
	use ApiTrait;

	/**
	 * Project info trait.
	 */
	use ProjectInfoTrait;

	/**
	 * Deprecated trait.
	 */
	use DeprecatedTrait;

	/**
	 * TailwindCSS trait.
	 */
	use TailwindTrait;
}
