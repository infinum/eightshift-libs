<?php

/**
 * Helpers for selectors.
 *
 * @package EightshiftLibs\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Helpers;

use EightshiftLibs\Exception\ComponentException;

/**
 * Class SelectorsTrait Helper
 */
trait SelectorsTrait
{
	/**
	 * Return a BEM class selector and check if Condition part is set.
	 *
	 * @param array<string>|bool|string $condition Check condition. Must be a truthy value!
	 *                                             Otherwise the result will be an empty string.
	 * @param string $block BEM Block selector.
	 * @param string $element BEM Element selector.
	 * @param string $modifier BEM Modifier selector.
	 *
	 * @return string
	 */
	public static function selector($condition, string $block, string $element = '', string $modifier = ''): string
	{
		// Early return for falsy conditions
		if (!$condition) {
			return '';
		}

		// Trim and cache block (required parameter)
		$block = trim($block);

		// Build selector efficiently - only trim when needed
		$selector = $block;

		if ($element !== '') {
			$element = trim($element);
			if ($element !== '') {
				$selector .= "__{$element}";
			}
		}

		if ($modifier !== '') {
			$modifier = trim($modifier);
			if ($modifier !== '') {
				$selector .= "--{$modifier}";
			}
		}

		return self::bem($block, $element, $modifier);
	}

	/**
	 * Return a BEM class selector.
	 *
	 * @param string $block BEM Block selector.
	 * @param string $element BEM Element selector.
	 * @param string $modifier BEM Modifier selector.
	 *
	 * @return string
	 */
	public static function bem(string $block, string $element = '', string $modifier = ''): string
	{
		// Trim and cache block (required parameter)
		$block = trim($block);

		// Build selector efficiently - only trim when needed
		$selector = $block;

		if ($element !== '') {
			$element = trim($element);
			if ($element !== '') {
				$selector .= "__{$element}";
			}
		}

		if ($modifier !== '') {
			$modifier = trim($modifier);
			if ($modifier !== '') {
				$selector .= "--{$modifier}";
			}
		}

		return $selector;
	}

	/**
	 * Create responsive selectors used for responsive attributes.
	 *
	 * Example:
	 * Helpers::responsiveSelectors($attributes['width'], 'width', $block_class);
	 *
	 * Output:
	 * block-column__width-large--4
	 *
	 * @param array<int|string, array<string>|bool|string> $items Array of breakpoints.
	 * @param string $selector Selector for this breakpoint.
	 * @param string $parent Parent block selector.
	 * @param boolean $useModifier If false you can use this selector for visibility.
	 *
	 * @return string
	 */
	public static function responsiveSelectors(array $items, string $selector, string $parent, bool $useModifier = true): string
	{
		// Early return for empty items
		if (empty($items)) {
			return '';
		}

		// Pre-allocate output array for better memory performance
		$output = [];

		// Cache selector pattern for performance
		$selectorBase = "{$parent}__{$selector}-";

		foreach ($items as $itemKey => $itemValue) {
			// Optimized type and value checking
			if ($itemValue === '' || $itemValue === false || is_array($itemValue)) {
				continue;
			}

			// Build selector efficiently
			if ($useModifier) {
				$output[] = $selectorBase . $itemKey . '--' . $itemValue;
			} else {
				$output[] = $selectorBase . $itemKey;
			}
		}

		// Use optimized classnames method
		return self::classnames($output);
	}

	/**
	 * Makes sure the output is string. Useful for converting an array of components into a string.
	 * If you pass an associative array it will output strings with keys, used for generating data-attributes from array.
	 *
	 * @param array<string, mixed>|string[]|string $variable Variable we need to convert into a string.
	 *
	 * @throws ComponentException When $variable is not a string or array.
	 *
	 * @return string
	 */
	public static function ensureString($variable): string
	{
		// Fast path for strings
		if (is_string($variable)) {
			return $variable;
		}

		if (is_array($variable)) {
			// Early return for empty arrays
			if (empty($variable)) {
				return '';
			}

			// Check if array is associative (has string keys or non-sequential numeric keys)
			$isAssociative = !array_is_list($variable);

			if ($isAssociative) {
				// For associative arrays, build data attributes
				$output = '';
				foreach ($variable as $key => $value) {
					$output .= $key . '="' . htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8') . '" ';
				}
				return rtrim($output); // Remove trailing space
			} else {
				// For sequential arrays, join elements
				return implode('', $variable);
			}
		}

		// Invalid type - throw exception
		throw ComponentException::throwNotStringOrArray($variable);
	}

	/**
	 * Converts an array of classes into a string which can be echoed.
	 *
	 * @param array<string> $classes Array of classes.
	 *
	 * @return string
	 */
	public static function clsx(array $classes): string
	{
		// Use array_filter with a more efficient callback and avoid trim
		return implode(' ', array_filter($classes, function ($class) {
			return $class !== '' && $class !== null && $class !== false;
		}));
	}
}
