<?php

/**
 * Helpers for Shortcode.
 *
 * @package EightshiftLibs\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Helpers;

/**
 * Class ShortcodeTrait Helper
 */
trait ShortcodeTrait
{
	/**
	 * Call a shortcode function by tag name.
	 *
	 * @param string $tag The shortcode whose function to call.
	 * @param array<mixed> $attr The attributes to pass to the shortcode function. Optional.
	 * @param string|null $content The shortcode's content. Default is null (none).
	 *
	 * @return string|bool False on failure, the result of the shortcode on success.
	 * @author J.D. Grimes
	 *
	 * @link https://codesymphony.co/dont-do_shortcode/
	 */
	public static function getShortcode(string $tag, array $attr = [], string $content = null)
	{
		global $shortcode_tags; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps

		if (!isset($shortcode_tags[$tag])) { // phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps
			return false;
		}

		return \call_user_func($shortcode_tags[$tag], $attr, $content, $tag); // phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps
	}
}
