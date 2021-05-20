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
				$output .= self::globalInner($itemValue, $itemKey);
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
	public static function globalInner(array $itemValues, string $itemKey): string
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
	public static function outputCssVariables(array $attributes, array $manifest, string $unique, array $globalManifest): string
	{
		$output = '';

		// Bailout if global breakpoints are missing.
		if (!isset($globalManifest['globalVariables']) || !isset($globalManifest['globalVariables']['breakpoints'])) {
			return '';
		}

		// Bailout if attributes or manifest is missing.
		if (!$attributes || !$manifest) {
			return '';
		}

		// Bailout if manifest is missing variables key.
		if (!isset($manifest['variables'])) {
			return '';
		}

		// Define variables from globalManifest.
		$breakpoints = $globalManifest['globalVariables']['breakpoints'];

		// Define variables from manifest.
		$variables = $manifest['variables'];

		// Get the initial data array.
		$data = self::prepareVariableData($breakpoints);

		// Check if component or block.
		$name = $manifest['componentClass'] ?? $attributes['blockClass'];

		// Check manifest for the attributes with variable key.
		// As this is not JS we can't simply get this data from attributes array so we need to do it manually.
		$defaultAttributes = array_keys(
			array_filter(
				$variables,
				function ($key) use ($attributes) {
					return !isset($attributes[$key]);
				},
				ARRAY_FILTER_USE_KEY
			)
		);

		// On frontend attributes are returned only the ones saved in the DB. So we check the manifest for the attributes with variable key and get the default value.
		if ($defaultAttributes) {
			$default = [];

			foreach ($defaultAttributes as $key) {
				if (isset($attributes[$key]['default'])) {
					$default[$key] = $attributes[$key]['default'];
				}
			}

			$attributes = array_merge($default, $attributes);
		}

		// Iterate each variable.
		foreach ($variables as $variableName => $variableValue) {
			// Bailout if variable is empty or not set.
			if (!isset($attributes[$variableName])) {
				continue;
			}

			// Constant for attributes set value (in db or default).
			$attributeValue = $attributes[$variableName] ?? [];

			// Make sure this works correctly for attributes which are toggles (booleans).
			if (is_bool($attributeValue)) {
				$attributeValue = $attributeValue ? 'true' : 'false';
			}

			// If type default or value.
			if (!self::arrayIsList($variableValue)) {
				$variableValue = $variableValue[$attributeValue] ?? [];
			}

			// Bailout if wrong type is provided.
			if (!is_array($variableValue)) {
				continue;
			}

			// Iterate variable array to check breakpoints.
			foreach ($variableValue as $breakpointItem) {
				// Define variables from breakpointItem.
				$breakpoint = $breakpointItem['breakpoint'] ?? 'default'; // If breakpoint is not set use default name.
				$inverse = $breakpointItem['inverse'] ?? false; // If inverse is not set use mobile first.
				$variable = $breakpointItem['variable'] ?? [];

				// Check if we are using mobile or desktop first. Mobile first is the default.
				$type = $inverse ? 'max' : 'min';

				// Iterate each data array to find the correct breakpoint.
				foreach ($data as $index => $item) {
					// Check if breakpoint and type match.
					if ($item['name'] === $breakpoint && $item['type'] === $type) {
						// Merge data variables with the new variables array.
						$data[$index]['variable'] = array_merge($item['variable'], self::variablesInner($variable, $attributeValue));
					}
				}
			}
		}

		// Loop data and provide correct selectors from data array.
		foreach ($data as $values) {
			// Define variables from values.
			$type = $values['type'];
			$value = $values['value'];
			$variable = $values['variable'];

			// Bailout if variables are empty.
			if (!$variable) {
				continue;
			}

			// Merge array of variables to string.
			$breakpointData = implode("\n", $variable);

			// If breakpoint value is 0 then don't wrap the media query around it.
			if ($value === 0) {
				$output .= ".{$name}[data-id='{$unique}'] {
						{$breakpointData}
					}
				";
			} else {
				$output .= "@media ({$type}-width: {$value}px) {
						.{$name}[data-id='{$unique}'] {
							{$breakpointData}
						}
					}
				";
			}
		}

		// Output manual output from the array of variables.
		$manual = isset($manifest['variablesCustom']) ? \esc_html(implode(";\n", $manifest['variablesCustom'])) : '';

		// Prepare final output for testing.
		$fullOutput = "
			{$output}
			{$manual}
		";

		// Check if final output is empty and and remove if it is.
		if (empty(trim($fullOutput))) {
			return '';
		}

		// Prepare output for manual variables.
		$finalManualOutput = $manual ? ".{$name}[data-id='{$unique}'] {
			{$manual}
		}" : '';

		// Output the style for CSS variables.
		return "<style>{$output} {$finalManualOutput}</style>";
	}

	/**
	 * Create initial array of data to be able to populate later.
	 *
	 * @param array $globalBreakpoints Global breakpoints from global manifest to set the correct output.
	 *
	 * @return array
	 */
	public static function prepareVariableData(array $globalBreakpoints): array
	{

		// Define the min and max arrays.
		$min = [];
		$max = [];

		// Loop the global breakpoints and populate the data.
		foreach ($globalBreakpoints as $itemKey => $itemValue) {
			// Initial inner object.
			$itemObject = [
				'name' => $itemKey,
				'value' => $itemValue,
				'variable' => [],
			];

			// Inner object for min values.
			$itemObjectMin = array_merge(
				[
					'type' => 'min',
				],
				$itemObject
			);

			// Inner object for max values.
			$itemObjectMax = array_merge(
				[
					'type' => 'max',
				],
				$itemObject
			);

			// Push both min and max to the defined arrays.
			$min[] = $itemObjectMin;
			$max[] = $itemObjectMax;
		};

		// Add default object to the top of the array.
		array_unshift(
			$min,
			[
				'type' => 'min',
				'name' => 'default',
				'value' => 0,
				'variable' => [],
			]
		);

		// Reverse order of max array.
		$max = array_reverse($max);

		// Add default object to the top of the array.
		array_unshift(
			$max,
			[
				'type' => 'max',
				'name' => 'default',
				'value' => 0,
				'variable' => [],
			]
		);

		// Merge both arrays.
		return array_merge($min, $max);
	}

	/**
	 * Internal helper to loop CSS Variables from array.
	 *
	 * @param array $variables Array of variables of CSS variables.
	 * @param mixed $attributeValue Original attribute value used in magic variable.
	 *
	 * @return array
	 */
	public static function variablesInner(array $variables, $attributeValue): array
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
				// Bailout if magic variable is empty.
				if (empty($attributeValue)) {
					continue;
				}

				$variableValue = str_replace('%value%', $attributeValue, $variableValue);
			}

			// Output the custom CSS variable by adding the attribute key + custom object key.
			$output[] = "--{$internalKey}: ${variableValue};";
		}

		return $output;
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
	 * Convert string from kebab to camel case
	 *
	 * @param string $string    String to convert.
	 * @param string $separator Separator to use for conversion.
	 *
	 * @return string
	 */
	public static function kebabToCamelCase(string $string, string $separator = '-'): string
	{
		return lcfirst(str_replace($separator, '', ucwords($string, $separator)));
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
