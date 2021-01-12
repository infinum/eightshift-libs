<?php

/**
 * The Shortcode specific functionality.
 *
 * @package EightshiftLibs\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Helpers;

/**
 * Class Shortcode
 */
class Shortcode
{

	/**
	 * Call a shortcode function by tag name.
	 *
	 * @author J.D. Grimes
	 * @link https://codesymphony.co/dont-do_shortcode/
	 *
	 * @param string      $tag The shortcode whose function to call.
	 * @param array       $attr The attributes to pass to the shortcode function. Optional.
	 * @param string|null $content The shortcode's content. Default is null (none).
	 *
	 * @return string|bool False on failure, the result of the shortcode on success.
	 */
	public static function getShortcode(string $tag, array $attr = [], $content = null)
	{
		global $shortcode_tags;

		if (!isset($shortcode_tags[$tag])) {
			return false;
		}

		return \call_user_func($shortcode_tags[$tag], $attr, $content, $tag);
	}
}
