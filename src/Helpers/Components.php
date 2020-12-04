<?php

/**
 * Helpers for components
 *
 * @package EightshiftLibs\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Helpers;

use EightshiftLibs\Exception\ComponentException;

/**
 * Helpers for components
 */
class Components
{

	/**
	 * Makes sure the output is string. Useful for converting an array of components into a string.
	 * If you pass an associative array it will output strings with keys, used for generating data-attributes from array.
	 *
	 * @param array|string $variable Variable we need to convert into a string.
	 *
	 * @throws ComponentException When $variable is not a string or array.
	 *
	 * @return string
	 */
	public static function ensureString($variable): string
	{
		$output = '';

		if (is_array($variable)) {
			$isAssociative = array_values($variable) === $variable;

			if ($isAssociative) {
				$output = implode('', $variable);
			} else {
				foreach ($variable as $key => $value) {
					$output .= $key . '="' . htmlspecialchars($value) . '" ';
				}
			}
		} elseif (is_string($variable)) {
			$output = $variable;
		} else {
			ComponentException::throwNotStringOrVariable($variable);
		}

		return $output;
	}

	/**
	 * Converts an array of classes into a string which can be echoed.
	 *
	 * @param array $classes Array of classes.
	 *
	 * @return string
	 */
	public static function classnames(array $classes): string
	{
		return trim(implode(' ', $classes));
	}

	/**
	 * Renders a components and (optionally) passes some attributes to it.
	 *
	 * Note about "parentClass" attribute: If provided, the component will be wrapped with a
	 * parent BEM selector. For example, if $attributes['parentClass'] === 'header' and $component === 'logo'
	 * are set, the component will be wrapped with a <div class="header__logo"></div>.
	 *
	 * @param string $component Component's name or full path (ending with .php).
	 * @param array  $attributes Array of attributes that's implicitly passed to component.
	 * @param string $parentPath If parent path is provides it will be appended to the file location.
	 *                            If not get_template_directory_uri() will be used as a default parent path.
	 *
	 * @throws \Exception When we're unable to find the component by $component.
	 *
	 * @return string
	 */
	public static function render(string $component, array $attributes = [], string $parentPath = '')
	{
		if (empty($parentPath)) {
			$parentPath = \get_template_directory();
		}

		// Detect if user passed component name or path.
		if (strpos($component, '.php') !== false) {
			$componentPath = "{$parentPath}/$component";
		} else {
			$componentPath = "{$parentPath}/src/Blocks/components/{$component}/{$component}.php";
		}

		if (!file_exists($componentPath)) {
			ComponentException::throwUnableToLocateComponent($componentPath);
		}

		ob_start();

		// Wrap component with parent BEM selector if parent's class is provided. Used
		// for setting specific styles for components rendered inside other components.
		if (isset($attributes['parentClass'])) {
			echo \wp_kses_post("<div class=\"{$attributes['parentClass']}__{$component}\">");
		}

		require $componentPath;

		if (isset($attributes['parentClass'])) {
			echo '</div>';
		}

		return trim((string) ob_get_clean());
	}

	/**
	 * Get manifest json. Generally used for getting block/components manifest.
	 *
	 * @param string $path Absolute path to manifest folder.
	 *
	 * @throws \Exception When we're unable to find the component by $component.
	 *
	 * @return array
	 */
	public static function getManifest(string $path): array
	{

		$manifest = "{$path}/manifest.json";

		if (!file_exists($manifest)) {
			throw ComponentException::throwUnableToLocateComponent($manifest);
		}

		return json_decode(implode(' ', (array)file($manifest)), true);
	}

	/**
	 * Create responsive selectors used for responsive attributes.
	 *
	 * Example:
	 * Components::responsiveSelectors($attributes['width'], 'width', $block_class);
	 *
	 * Output:
	 * block-column__width-large--4
	 *
	 * @param array   $items Array of breakpoints.
	 * @param string  $selector Selector for this breakpoint.
	 * @param string  $parent Parent block selector.
	 * @param boolean $useModifier If false you can use this selector for visibility.
	 *
	 * @return string
	 */
	public static function responsiveSelectors(array $items, string $selector, string $parent, bool $useModifier = true)
	{
		$output = [];

		foreach ($items as $itemKey => $itemValue) {
			if ((gettype($itemValue) === 'string' && $itemValue === '') || gettype($itemValue) === 'boolean' && $itemValue === false) {
				continue;
			}

			if ($useModifier) {
				$output[] = "{$parent}__{$selector}-{$itemKey}--{$itemValue}";
			} else {
				$output[] = "{$parent}__{$selector}-{$itemKey}";
			}
		}

		return static::classnames($output);
	}

	/**
	 * Check if attribute exist in attributes list and add default value if not.
	 *
	 * @param string $key Key to check.
	 * @param array  $attributes Array of attributes.
	 * @param array  $manifest Array of default attributes from manifest.json.
	 *
	 * @return mixed
	 */
	public static function checkAttr(string $key, array $attributes, array $manifest)
	{
		$manifestKey = $manifest['attributes'][$key];
		$defaultType = $manifestKey['type'];

		switch ($defaultType) {
			case 'boolean':
				$defaultValue = isset($manifestKey['default']) ? $manifestKey['default'] : false;
				break;
			case 'array':
			case 'object':
				$defaultValue = isset($manifestKey['default']) ? $manifestKey['default'] : [];
				break;
			default:
				$defaultValue = isset($manifestKey['default']) ? $manifestKey['default'] : '';
				break;
		}

		return isset($attributes[$key]) ? $attributes[$key] : $defaultValue;
	}


	/**
	 * Return BEM selector for html class and check if Block element is set.
	 *
	 * @param string $block BEM Block selector.
	 * @param string $element BEM Element selector.
	 * @param string $modifier BEM Modifier selector.
	 *
	 * @return string
	 */
	public static function selectorB(string $block, string $element = '', string $modifier = ''): string
	{
		$fullModifier = '';
		$fullElement = '';

		if ($element) {
			$fullElement = "__{$element}";
		}

		if ($modifier) {
			$fullModifier = "--{$modifier}";
		}

		return $block ? "{$block}{$fullElement}{$fullModifier}"  : '';
	}

	/**
	 * Return BEM selector for html class and check if element is set.
	 *
	 * @param string $block BEM Block selector.
	 * @param string $element BEM Element selector.
	 * @param string $modifier BEM Modifier selector.
	 *
	 * @return string
	 */
	public static function selectorE(string $block, string $element, string $modifier = ''): string
	{
		$fullModifier = '';

		if ($modifier) {
			$fullModifier = "--{$modifier}";
		}

		return $element ? "{$block}__{$element}{$fullModifier}"  : '';
	}

	/**
	 * Return BEM selector for html class and check if Modifier element is set.
	 *
	 * @param string $block BEM Block selector.
	 * @param string $element BEM Element selector.
	 * @param string $modifier BEM Modifier selector.
	 *
	 * @return string
	 */
	public static function selectorM(string $block, string $element, string $modifier): string
	{
		return $modifier ? "{$block}__{$element}--{$modifier}"  : '';
	}

	/**
	 * Return BEM selector for html class and check all conditions from checkAttr method.
	 *
	 * @param string $block BEM Block selector.
	 * @param string $element BEM Element selector.
	 * @param string $key Key to check.
	 * @param array  $attributes Array of attributes.
	 * @param array  $manifest Array of default attributes from manifest.json.
	 *
	 * @return string
	 */
	public static function selector(string $block, string $element, string $key, array $attributes, array $manifest): string
	{

		$modifier = self::checkAttr($key, $attributes, $manifest);

		return self::selectorM($block, $element, $modifier);
	}

	/**
	 * Return BEM selector for html class and check if Custom condition is set.
	 *
	 * @param bool   $condition Check condition.
	 * @param string $block BEM Block selector.
	 * @param string $element BEM Element selector.
	 * @param string $modifier BEM Modifier selector.
	 *
	 * @return string
	 */
	public static function selectorCustom(bool $condition, string $block, string $element = '', string $modifier = ''): string
	{
		return $condition ? self::selectorB($block, $element, $modifier) : '';
	}
}
