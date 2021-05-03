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
			throw ComponentException::throwNotStringOrArray($variable);
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
	 * @param bool   $useComponentDefaults If true the helper will fetch component manifest and merge default attributes in the original attributes list.
	 *
	 * @throws ComponentException When we're unable to find the component by $component.
	 *
	 * @return string
	 */
	public static function render(string $component, array $attributes = [], string $parentPath = '', bool $useComponentDefaults = false)
	{
		if (empty($parentPath)) {
			$parentPath = \get_template_directory();
		}

		/**
		 * Detect if user passed component name or path.
		 *
		 * If the path was passed, we need to get the component name, in case the
		 * parentClass attribute was added, because the class of the wrapper need to look like
		 *
		 * parentClass__componentName
		 *
		 * not
		 *
		 * parentClass__componentName.php
		 */
		if (strpos($component, '.php') !== false) {
			$componentPath = "{$parentPath}/$component";

			if ($useComponentDefaults) {
				$manifest = self::getManifest($parentPath);
			}
		} else {
			$componentPath = "{$parentPath}/src/Blocks/components/{$component}/{$component}.php";

			if ($useComponentDefaults) {
				$manifest = self::getManifest("{$parentPath}/src/Blocks/components/{$component}");
			}
		}

		if (!file_exists($componentPath)) {
			throw ComponentException::throwUnableToLocateComponent($componentPath);
		}

		if ($useComponentDefaults && isset($manifest['attributes'])) {
			$attributes =  self::getDefaultRenderAttributes($manifest, $attributes);
		}

		ob_start();

		// Wrap component with parent BEM selector if parent's class is provided. Used
		// for setting specific styles for components rendered inside other components.
		if (isset($attributes['parentClass'])) {
			$component = str_replace('.php', '', $component);
			printf('<div class="%s">', \esc_attr("{$attributes['parentClass']}__{$component}")); // phpcs:ignore Eightshift.Security.CustomEscapeOutput.OutputNotEscaped
		}

		require $componentPath;

		if (isset($attributes['parentClass'])) {
			echo '</div>'; // phpcs:ignore Eightshift.Security.CustomEscapeOutput.OutputNotEscaped
		}

		return trim((string) ob_get_clean());
	}

	/**
	 * Merges attributes array with the manifet default attributes.
	 *
	 * @param array $manifest   Block/Component manifest data.
	 * @param array $attributes Block/Component rendered attributes data.
	 *
	 * @return array
	 */
	public static function getDefaultRenderAttributes(array $manifest, array $attributes): array
	{
		$defaultAttributes = [];

		foreach ($manifest['attributes'] as $itemKey => $itemValue) {
			if (isset($itemValue['default'])) {
				$defaultAttributes[$itemKey] = $itemValue['default'];
			}
		}

		return array_merge($defaultAttributes, $attributes);
	}

	/**
	 * Get manifest json. Generally used for getting block/components manifest.
	 *
	 * @param string $path Absolute path to manifest folder.
	 *
	 * @throws ComponentException When we're unable to find the component by $component.
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
	 * @param string $componentName The real component name.
	 *
	 * @throws \Exception When we're unable to find the component by $component.
	 *
	 * @return mixed
	 */
	public static function checkAttr(string $key, array $attributes, array $manifest, string $componentName = '')
	{

		if (isset($attributes[$key])) {
			return $attributes[$key];
		} else {
			$manifestKey = $manifest['attributes'][$key] ?? null;

			if ($manifestKey === null) {
				throw new \Exception("{$key} key does not exist in the {$componentName} component. Please check your implementation. Check if your {$key} attribut exists in the component's manifest.json");
			}

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

			return $defaultValue;
		}
	}

	/**
	 * Map and check attributes for responsive object.
	 *
	 * @param string $keyName Key name to find in the responsiveAttributes object.
	 * @param array  $attributes Array of attributes.
	 * @param array  $manifest Array of default attributes from manifest.json.
	 * @param string $componentName The real component name.
	 *
	 * @throws \Exception If missing responsiveAttributes or keyName in responsiveAttributes.
	 * @throws \Exception If missing keyName in responsiveAttributes.
	 *
	 * @return mixed
	 */
	public static function checkAttrResponsive(string $keyName, array $attributes, array $manifest, string $componentName = '')
	{
		$output = [];

		if (!isset($manifest['responsiveAttributes'])) {
			throw new \Exception("It looks like you are missing the responsiveAttributes key in your {$componentName} manifest.");
		}

		if (!isset($manifest['responsiveAttributes'][$keyName])) {
			throw new \Exception("It looks like you are missing the {$keyName} key in your manifest responsiveAttributes array.");
		}

		foreach ($manifest['responsiveAttributes'][$keyName] as $key => $value) {
			$output[$key] = self::checkAttr($value, $attributes, $manifest, $componentName);
		}

		return $output;
	}

	/**
	 * Return a BEM class selector and check if Condition part is set.
	 *
	 * @param mixed  $condition Check condition.
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

		$element = trim($element);
		$modifier = trim($modifier);
		$block = trim($block);

		if (!empty($element)) {
			$fullElement = "__{$element}";
		}

		if (!empty($modifier)) {
			$fullModifier = "--{$modifier}";
		}

		return $condition ? "{$block}{$fullElement}{$fullModifier}"  : '';
	}

	/**
	 * Get Global Manifest.json and return globalVariables as css variables.
	 *
	 * @param array $globalManifest Array of global variables data.
	 * @return string
	 */
	public static function outputCssVariablesGlobal(array $globalManifest): string
	{
		$output = '';

		if (!$globalManifest || !isset($globalManifest['globalVariables'])) {
			return $output;
		}

		foreach ($globalManifest['globalVariables'] as $itemKey => $itemValue) {
			$itemKey = self::camelToKebabCase($itemKey);

			if (gettype($itemValue) === 'array') {
				$output .= self::outputCssVariablesGlobalInner($itemValue, $itemKey);
			} else {
				$output .= "--global-{$itemKey}: {$itemValue};\n";
			}
		}

		return $output ? "
			<style>
				:root {
					{$output}
				}
			</style>
		" : '';
	}

	/**
	 * Process and return global css variables based on the type.
	 *
	 * @param array  $itemValues Values of data to check.
	 * @param string $itemKey    Item key to check.
	 *
	 * @return string
	 */
	public static function outputCssVariablesGlobalInner(array $itemValues, string $itemKey): string
	{
		$output = '';

		foreach ($itemValues as $key => $value) {
			$key = self::camelToKebabCase((string)$key);
			$itemKey = self::camelToKebabCase((string)$itemKey);

			switch ($itemKey) {
				case 'colors':
					$output .= "--global-{$itemKey}-{$value['slug']}: {$value['color']};\n";
					break;
				case 'gradients':
					$output .= "--global-{$itemKey}-{$value['slug']}: {$value['gradient']};\n";
					break;
				case 'font-sizes':
					$output .= "--global-{$itemKey}-{$value['slug']}: {$value['slug']};\n";
					break;
				default:
					$output .= "--global-{$itemKey}-{$key}: {$value};\n";
					break;
			}
		}

		return $output;
	}

	/**
	 * Get component/block options and process them in CSS variables.
	 *
	 * @param array  $attributes Built attributes.
	 * @param array  $manifest Component/block manifest data.
	 * @param string $unique Unique key.
	 * @param array  $globalManifest Global manifest array.
	 *
	 * @return string
	 */
	public static function outputCssVariables(array $attributes, array $manifest, string $unique, array $globalManifest = []): string
	{
		$data = [];
		$output = '';

		if (!$attributes || !$manifest) {
			return $output;
		}

		// Check if component or block.
		$name = $manifest['componentClass'] ?? $attributes['blockClass'];

		// Check manifest for the attributes with variable key.
		// As this is not JS we can't simply get this data from attributes array so we need to do it manually.
		$defaultAttributes = array_filter(
			$manifest['attributes'],
			function ($item) {
				return isset($item['variable']);
			}
		);

		// On frontend attributes are returned only the ones saved in the DB. So we check the manifest for the attributes with variable key and get the default value.
		if ($defaultAttributes) {
			$default = [];

			foreach ($defaultAttributes as $key => $value) {
				if (isset($value['default'])) {
					$default[$key] = $value['default'];
				}
			}

			$attributes = array_merge($default, $attributes);
		}

		// Iterate each attribute and make corrections.
		foreach ($attributes as $attributeName => $attributeValue) {
			// Bailout if attribute is not using variables.
			if (!isset($manifest['attributes'][$attributeName]) || !isset($manifest['attributes'][$attributeName]['variable'])) {
				continue;
			}

			// Bailout if variables key is not existing or attribute key is non existing in variables array.
			if (!isset($manifest['variables']) || !isset($manifest['variables'][$attributeName])) {
				continue;
			}

			// Check type of variable.
			$variableType = $manifest['attributes'][$attributeName]['variable'];
			$variables = $manifest['variables'][$attributeName];

			switch ($variableType) {
				case 'value':
					// Bailout if attribute value doesn't exist in variables.
					if (!isset($variables[$attributeValue])) {
						break;
					}

					// Bailout if attribute value is not array.
					if (!is_array($variables[$attributeValue])) {
						break;
					}

					$data = self::outputCssVariablesResponsive($variables[$attributeValue], $attributeValue, $globalManifest, $data);
					break;

				default:
					$data = self::outputCssVariablesResponsive($variables, $attributeValue, $globalManifest, $data);
					break;
			}
		}

		// Loop data and provide correct selectors from data object.
		if (!empty($data)) {
			foreach ($data as $breakpoint => $breakpointData) {
				// Bailout if output array is empty.
				if (empty($breakpointData)) {
					continue;
				}

				// If this is default dont wrap the media query around it.
				$breakpointData = implode("\n", $breakpointData);

				if ($breakpoint === 'default') {
					$output .= ".{$name}[data-id='{$unique}'] {
							{$breakpointData}
						}
					";
				} else {
					$output .= "@media ({$breakpoint}) {
							.{$name}[data-id='{$unique}'] {
								{$breakpointData}
							}
						}
					";
				}
			}
		}

		// Output manual output from the array of variables.
		$manual = isset($manifest['variablesCustom']) ? \esc_html(implode(";\n", $manifest['variablesCustom'])) : '';

		// Prepare final output.
		$finalOutput = "
			{$output}
			{$manual}
		";

		// Check if final output is empty and and remove if it is.
		if (empty(trim($finalOutput))) {
			return '';
		}

		// Output the style for CSS variables.
		return "<style>{$finalOutput}</style>";
	}

	/**
	 * Internal helper to loop CSS Variables from array.
	 *
	 * @param array $variables Array of variables of CSS variables.
	 * @param mixed $attributeValue Original attribute value used in magic variable.
	 *
	 * @return array
	 */
	public static function outputCssVariablesInner(array $variables, $attributeValue): array
	{
		$output = [];

		// Bailout if provided list is not an object.
		if (self::arrayIsList($variables)) {
			return $output;
		}

		// Iterate each attribute and make corrections.
		foreach ($variables as $variableKey => $variableValue) {
			// Convert to correct case.
			$internalKey = self::camelToKebabCase($variableKey);

			// If value contains magic variable swap that variable with original attribute value.
			if (strpos($variableValue, '%value%') !== false) {
				$variableValue = str_replace('%value%', $attributeValue, $variableValue);
			}

			// Output the custom CSS variable by adding the attribute key + custom object key.
			$output[] = "--{$internalKey}: ${variableValue};";
		}

		return $output;
	}

	/**
	 * Internal helper to loop CSS Variables from array of objects in an responsive manner.
	 *
	 * @param array $breakpoints Breakpoints array list of CSS variables.
	 * @param mixed $attributeValue Original attribute value used in magic variable.
	 * @param array $globalManifest Global manifest array.
	 * @param array $data Data array from parent.
	 *
	 * @returns array
	 */
	public static function outputCssVariablesResponsive(array $breakpoints, $attributeValue, array $globalManifest, array $data): array
	{

		// Bailout if globalVariables or breakpoints is missing.
		if (!isset($globalManifest['globalVariables']) || !isset($globalManifest['globalVariables']['breakpoints'])) {
			return $data;
		}

		// Iterate each attribute and make corrections.
		foreach ($breakpoints as $item) {
			$breakpoint = $item['breakpoint'] ?? '';
			$inverse = $item['inverse'] ?? false;
			$variable = $item['variable'] ?? [];

			// Find the actual value of the breakpoint.
			$breakpointValue = $globalManifest['globalVariables']['breakpoints'][$breakpoint] ?? '';

			// Output CSS variables from the variables array.
			$innerValue = self::outputCssVariablesInner($variable, $attributeValue);

			// Check if we are using mobile or desktop first. Mobile first is the default.
			$orderBreakpint = $inverse ? 'max-width' : 'min-width';

			// Output normal selector if breakpoint is not defined (used for top level element like mobile).
			// Else wrap it in media query condition.
			if (empty($breakpointValue)) {
				if (!isset($data['default'])) {
					$data['default'] = [];
				}

				$data['default'] = array_merge($data['default'], $innerValue);
			} else {
				$customKey = "${orderBreakpint}: ${breakpointValue}px";

				if (!isset($data[$customKey])) {
					$data[$customKey] = [];
				}

				$data[$customKey] = array_merge($data[$customKey], $innerValue);
			}
		};

		return $data;
	}

	/**
	 * Return unique ID for block processing.
	 *
	 * @return string
	 */
	public static function getUnique(): string
	{
		return md5(uniqid((string)\wp_rand(), true));
	}

	/**
	 * Convert string from camel to kebab case
	 *
	 * @param string $string String to convert.
	 *
	 * @return string
	 */
	public static function camelToKebabCase(string $string): string
	{
		$replace = preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '-$0', $string) ?? '';
		return ltrim(strtolower($replace), '-');
	}

	/**
	 * Check if provided array is associative or sequential. Will return true if array is sequential.
	 *
	 * @param array $array Array to check.
	 *
	 * @return boolean
	 */
	public static function arrayIsList(array $array): bool
	{
		$expectedKey = 0;
		foreach ($array as $i => $value) {
			if ($i !== $expectedKey) {
				return false;
			}
			$expectedKey++;
		}

		return true;
	}

	/**
	 * Output only attributes that are used in the component and remove everything else.
	 *
	 * @param array   $attributes Object of attributes from block/component.
	 * @param string  $realName Old key to use, generally this is the name of the block/component.
	 * @param string  $newName New key to use to rename attributes.
	 * @param boolean $isBlock Check if helper is used on block or component.
	 * @param array   $globalData Global data of block, components, etc.
	 *
	 * @return array
	 */
	public static function props(array $attributes, string $realName, string $newName = '', bool $isBlock = false, array $globalData): array // phpcs:ignore PEAR.Functions.ValidDefaultValue.NotAtEnd
	{

		$newNameInternal = $newName;

		// Check if newName key is passed if not use the default one from block/component name.
		if (!$newName) {
			$newNameInternal = $realName;
		}

		$output = [];

		// If component use components dependency tree.
		$dependency = $globalData['components'][$realName];

		// If block use blocks dependency tree.
		if ($isBlock) {
			$dependency = $globalData['blocks'][$realName];
		}

		// If dependency is empty put the name in the array for the easier checks later on.
		if (!$dependency) {
			$dependency = [$newNameInternal];
		}

		// Add the current component name to the dependency array.
		$dependency[] = $newNameInternal;

		foreach ($attributes as $key => $value) {
			$result = false;
			foreach ($dependency as $element) {
				if ($element === substr($key, 0, strlen($element))) {
					$result =  true;
				}
			}

			// Check if attributes key exists in the dependency by comparing the keys partial string.
			if ($result) {
				$newKey = $key;

				// Change the name of the key if they are different.
				if ($realName !== $newNameInternal) {
					$newKey = $realName . substr($key, strlen($newNameInternal));
				}

				$output[$newKey] = $value;
			}
		}

		// Append componentName for usage.
		$output['componentName'] = $newNameInternal;

		return $output;
	}

	/**
	 * Flatten multidimensional array in to a single array.
	 *
	 * @param array $array Array to itearate.
	 *
	 * @return array
	 */
	public static function flattenArray(array $array): array
	{
		$return = [];

		array_walk_recursive(
			$array,
			function ($a) use (&$return) {
				$return[] = $a;
			}
		);

		return $return;
	}
}
