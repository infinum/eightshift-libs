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
		$fullModifier = '';
		$fullElement = '';

		$element = \trim($element);
		$modifier = \trim($modifier);
		$block = \trim($block);

		if (!empty($element)) {
			$fullElement = "__{$element}";
		}

		if (!empty($modifier)) {
			$fullModifier = "--{$modifier}";
		}

		return $condition ? "{$block}{$fullElement}{$fullModifier}"  : '';
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
		$output = [];

		foreach ($items as $itemKey => $itemValue) {
			if (
				(\is_string($itemValue) && $itemValue === '') ||
				(\is_bool($itemValue) && $itemValue === false) ||
				\is_array($itemValue)
			) {
				continue;
			}

			if ($useModifier) {
				$output[] = "{$parent}__{$selector}-{$itemKey}--{$itemValue}";
			} else {
				$output[] = "{$parent}__{$selector}-{$itemKey}";
			}
		}

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
		$output = '';

		if (\is_array($variable)) {
			$isAssociative = \array_values($variable) === $variable;

			if ($isAssociative) {
				$output = \implode('', $variable);
			} else {
				foreach ($variable as $key => $value) {
					$output .= $key . '="' . \htmlspecialchars($value) . '" ';
				}
			}
		} elseif (\is_string($variable)) {
			$output = $variable;
		} else {
			throw ComponentException::throwNotStringOrArray($variable);
		}

		return $output;
	}

	/**
	 * Converts an array of classes into a string which can be echoed.
	 *
	 * @param array<string> $classes Array of classes.
	 *
	 * @return string
	 */
	public static function classnames(array $classes): string
	{
		return \trim(\implode(' ', \array_filter($classes)));
	}
}
