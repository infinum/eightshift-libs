<?php

/**
 * Helpers for output css variables.
 *
 * @package EightshiftLibs\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Helpers;

/**
 * Class OutputCssVariablesTrait Helper
 */
trait CssVariablesTrait
{
	/**
	 * Get Global Manifest.json and return globalVariables as CSS variables.
	 *
	 * @param array<string, mixed> $globalManifest Global manifest array. - Deprecated.
	 *
	 * @return string
	 */
	public static function outputCssVariablesGlobal(array $globalManifest = []): string
	{
		$output = '';

		foreach (Components::getSettingsGlobalVariables() as $itemKey => $itemValue) {
			$itemKey = Components::camelToKebabCase($itemKey);

			if (\gettype($itemValue) === 'array') {
				$output .= self::globalInner($itemValue, $itemKey);
			} else {
				$output .= "--global-{$itemKey}: {$itemValue};\n";
			}
		}

		$id = Components::getConfigOutputCssSelectorName();

		$output = "<style id='{$id}-global'>:root {{$output}}</style>";

		if (Components::getConfigOutputCssOptimize()) {
			$output = \str_replace(["\n", "\r"], '', $output);
		}

		return $output;
	}

	/**
	 * Get component/block options and process them in CSS variables.
	 *
	 * @param array<string, mixed> $attributes Built attributes.
	 * @param array<string, mixed> $manifest Component/block manifest data.
	 * @param string $unique Unique key.
	 * @param array<string, mixed> $globalManifest Global manifest array.
	 * @param string $customSelector Output custom selector to use as a style prefix.
	 *
	 * @return string
	 */
	public static function outputCssVariables(array $attributes, array $manifest, string $unique, array $globalManifest = [], string $customSelector = ''): string
	{
		// Bailout if manifest is missing variables key.
		if (!isset($manifest['variables']) && !isset($manifest['variablesCustom'])) {
			return '';
		}

		// Define variables from globalManifest.
		$breakpoints = Components::getSettingsGlobalVariablesBreakpoints();

		// Sort breakpoints in ascending order.
		\asort($breakpoints);

		$defaultBreakpoints = self::getDefaultBreakpoints($breakpoints);

		// Define variables from manifest.
		$variables = $manifest['variables'] ?? [];

		// Define responsiveAttributes from manifest.
		$responsiveAttributes = $manifest['responsiveAttributes'] ?? [];

		// Get the initial data array.
		$data = self::prepareVariableData($breakpoints);

		// Check if component or block.
		$name = $manifest['componentClass'] ?? $attributes['blockClass'];

		if ($customSelector !== '') {
			$name = $customSelector;
		}

		// Check manifest for the attributes with variable key.
		// As this is not JS we can't simply get this data from attributes array so we need to do it manually.
		$defaultAttributes = \array_keys(
			\array_filter(
				$variables,
				static function ($key) use ($attributes) {
					return !isset($attributes[$key]);
				},
				\ARRAY_FILTER_USE_KEY
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

			$attributes = \array_merge($default, $attributes);
		}

		// Iterate each responsiveAttribute from responsiveAttributes that appears in variables field.
		if (!empty($responsiveAttributes)) {
			$responsiveVariables = self::setupResponsiveVariables($responsiveAttributes, $variables);
			$data = self::setVariablesToBreakpoints($attributes, $responsiveVariables, $data, $manifest, $defaultBreakpoints);
		}

		// Iterate each variable.
		if (!empty($variables)) {
			$data = self::setVariablesToBreakpoints($attributes, $variables, $data, $manifest, $defaultBreakpoints);
		}

		// Load normal styles if server side render is used.
		$ssr = $attributes['blockSsr'] ?? false;

		// If default output just echo.
		if (!Components::getConfigOutputCssGlobally() || $ssr) {
			return self::getCssVariablesTypeDefault($name, $data, $manifest, $unique);
		}

		// Set inline styles.
		Components::setStyle(self::getCssVariablesTypeInline($name, $data, $manifest, $unique));

		return '';
	}

	/**
	 * Output css variables as a one inline style tag. Used with wp_footer filter.
	 *
	 * @return string
	 */
	public static function outputCssVariablesInline(): string
	{
		// If default output just exit.
		if (!Components::getConfigOutputCssGlobally()) {
			return '';
		}

		// Prepare final output.
		$output = '';

		$styles = Components::getStyles();

		// Bailout if styles are missing.
		if ($styles) {
			// Define variables from globalManifest.
			$breakpointsData = self::getSettingsGlobalVariablesBreakpoints();

			// Sort breakpoints in ascending order.
			\asort($breakpointsData);

			// Populate min values.
			$breakpointsMin = \array_map(
				static function ($item) {
					return "min---{$item}";
				},
				\array_values($breakpointsData)
			);
			// Append 0 value.
			\array_unshift($breakpointsMin, 'min---0');

			// Populate max values.
			$breakpointsMax = \array_map(
				static function ($item) {
					return "max---{$item}";
				},
				\array_reverse(\array_values($breakpointsData))
			);
			// Append 0 value.
			\array_unshift($breakpointsMax, 'max---0');

			// Return empty array of items.
			$breakpoints = \array_map(
				static function () {
					return '';
				},
				\array_flip(\array_values(\array_merge($breakpointsMin, $breakpointsMax)))
			);

			// Loop styles.
			foreach ($styles as $style) {
				$name = $style['name'] ?? '';
				$unique = $style['unique'] ?? '';
				$variables = $style['variables'] ?? [];

				// Bailout if variables are missing.
				if (!$variables) {
					continue;
				}

				$uniqueSelector = "[data-id='{$unique}']";

				if (!$unique) {
					$uniqueSelector = '';
				}

				foreach ($variables as $data) {
					$type = $data['type'] ?? '';
					$value = $data['value'] ?? '';
					$variable = $data['variable'] ?? '';

					// Bailout if variable is missing.
					if (!$variable) {
						continue;
					}

					// Bailout if breakpont is missing.
					if (!isset($breakpoints["{$type}---{$value}"])) {
						continue;
					}

					// Populate breakpoint.
					$breakpoints["{$type}---{$value}"] .= "\n.{$name}{$uniqueSelector}{\n{$variable}\n} ";
				}
			}

			// Loop breakpoints in correct order.
			foreach ($breakpoints as $breakpointKey => $breakpointValue) {
				$breakpointKey = \explode('---', $breakpointKey);

				$type = $breakpointKey[0] ?? '';
				$value = $breakpointKey[1] ?? '';

				// Bailout if empty value.
				if (!$breakpointValue) {
					continue;
				}

				// If value is 0 then this breakpoint has no media query.
				if ($value === '0') {
					$output .= "{$breakpointValue}\n";
				} else {
					$output .= "\n@media ({$type}-width:{$value}px){{$breakpointValue}}\n ";
				}
			}
		}

		// Remove newlines is config is set.
		if (Components::getConfigOutputCssOptimize()) {
			$output = \str_replace(["\n", "\r"], '', $output);
		}

		// Add additional style from config settings.
		$additionalStyles = Components::getConfigOutputCssGloballyAdditionalStyles();
		$additionalStylesOutput = $additionalStyles ? \esc_html(\implode(";\n", $additionalStyles)) : '';

		$selector = Components::getConfigOutputCssSelectorName();

		return "<style id='{$selector}'>{$output} {$additionalStylesOutput}</style>";
	}

	/**
	 * Convert a hex color into RGB values.
	 *
	 * @param string $hex Input hex color.
	 *
	 * @return string
	 */
	public static function hexToRgb(string $hex): string
	{
		// Remove the # at the beginning and filter out invalid hex characters.
		$hex = \preg_replace("/[^0-9A-Fa-f]/", '', $hex);

		$length = \strlen($hex);

		if ($length === 3) {
			$r = \hexdec(\str_repeat(\substr($hex, 0, 1), 2));
			$g = \hexdec(\str_repeat(\substr($hex, 1, 1), 2));
			$b = \hexdec(\str_repeat(\substr($hex, 2, 1), 2));
		} elseif ($length === 6) {
			$r = \hexdec(\substr($hex, 0, 2));
			$g = \hexdec(\substr($hex, 2, 2));
			$b = \hexdec(\substr($hex, 4, 2));
		} else {
			$r = '0';
			$g = '0';
			$b = '0';
		}

		return "{$r} {$g} {$b}";
	}

	/**
	 * Return unique ID for block processing.
	 *
	 * @return string
	 */
	public static function getUnique(): string
	{
		return \md5(\uniqid((string)\wp_rand(), true));
	}

	/**
	 * Return CSS variables in default type. On the place where it was called.
	 *
	 * @param string $name Output css selector name.
	 * @param array<mixed> $data Data prepared for checking.
	 * @param array<mixed> $manifest Component/block manifest data.
	 * @param string $unique Unique key.
	 *
	 * @return string
	 */
	private static function getCssVariablesTypeDefault(string $name, array $data, array $manifest, string $unique): string
	{
		$output = '';

		$uniqueSelector = "[data-id='{$unique}']";

		if (!$unique) {
			$uniqueSelector = '';
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
			$breakpointData = \implode("\n", $variable);

			// If breakpoint value is 0 then don't wrap the media query around it.
			if ($value === 0) {
				$output .= "\n .{$name}{$uniqueSelector}{\n{$breakpointData}\n}";
			} else {
				$output .= "\n @media ({$type}-width:{$value}px){\n.{$name}{$uniqueSelector}{\n{$breakpointData}\n}\n}";
			}
		}

		// Output manual output from the array of variables.
		$manual = isset($manifest['variablesCustom']) ? \esc_html(\implode(";\n", $manifest['variablesCustom'])) : '';

		// Prepare final output for testing.
		$fullOutput = "
			{$output}
			{$manual}
		";

		// Check if final output is empty and and remove if it is.
		if (empty(\trim($fullOutput))) {
			return '';
		}

		// Prepare output for manual variables.
		$finalManualOutput = $manual ? "\n .{$name}{$uniqueSelector}{\n{$manual}\n}" : '';

		if (Components::getConfigOutputCssOptimize()) {
			$output = \str_replace(["\n", "\r"], '', $output);
			$finalManualOutput = \str_replace(["\n", "\r"], '', $finalManualOutput);
		}

		// Output the style for CSS variables.
		return "<style>{$output} {$finalManualOutput}</style>";
	}

	/**
	 * Get css variables in inline type. In one place in dom.
	 *
	 * @param string $name Output css selector name.
	 * @param array<mixed> $data Data prepared for checking.
	 * @param array<mixed> $manifest Component/block manifest data.
	 * @param string $unique Unique key.
	 *
	 * @return array<mixed>
	 */
	private static function getCssVariablesTypeInline(string $name, array $data, array $manifest, string $unique): array
	{
		// Prepare output style object.
		$styles = [
			'name' => $name,
			'unique' => $unique,
			'variables' => [],
		];

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
			$breakpointData = \implode("\n", $variable);

			// If breakpoint value is 0 then don't wrap the media query around it.
			$styles['variables'][] = [
				'type' => $type,
				'variable' => $breakpointData,
				'value' => $value,
			];
		}

		// Output manual output from the array of variables.
		$manual = isset($manifest['variablesCustom']) ? \esc_html(\implode(";\n", $manifest['variablesCustom'])) : '';

		// Output to global if flag is set.
		if ($manual) {
			$styles['variables'][] = [
				'type' => 'min',
				'variable' => $manual,
				'value' => 0,
			];
		}

		// Bailout if no styles is added.
		if (!$styles['variables']) {
			return [];
		}

		return $styles;
	}

	/**
	 * Process and return global css variables based on the type.
	 *
	 * @param array<string, mixed> $itemValues Values of data to check.
	 * @param string $itemKey Item key to check.
	 *
	 * @return string
	 */
	private static function globalInner(array $itemValues, string $itemKey): string
	{
		$output = '';

		foreach ($itemValues as $key => $value) {
			$key = Components::camelToKebabCase((string)$key);
			$itemKey = Components::camelToKebabCase((string)$itemKey);

			switch ($itemKey) {
				case 'colors':
					$output .= "--global-{$itemKey}-{$value['slug']}: {$value['color']};\n";

					$rgbValues = self::hexToRgb($value['color']);
					$output .= "--global-{$itemKey}-{$value['slug']}-values: {$rgbValues};\n";
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
	 * Sets up a breakpoint value to responsive attribute objects from responsiveAttribute object.
	 *
	 * @param array<string, mixed> $attributeVariables Array of attribute variables object.
	 * @param string $breakpointName Breakpoint name from responsiveAttribute's breakpoint in block's/component's manifest.
	 * @param integer $breakpointIndex Index of responsiveAttribute's breakpoint in manifest.
	 * @param integer $numberOfBreakpoints Number of responsiveAttribute breakpoints in block's/component's manifest.
	 *
	 * @return array<int, mixed>
	 */
	private static function setBreakpointResponsiveVariables(
		array $attributeVariables,
		string $breakpointName,
		int $breakpointIndex,
		int $numberOfBreakpoints
	): array {
		$breakpointAttributeValues = [];
		foreach ($attributeVariables as $attributeVariablesObject) {
			/**
			 * Calculate default breakpoint index based on order of the breakpoint, inverse property
			 * and number of properties in responsiveAttributeObject.
			 */
			$defaultBreakpointIndex = (isset($attributeVariablesObject['inverse']) && $attributeVariablesObject['inverse']) ? 0 : ((int) $numberOfBreakpoints - 1);

			// Expanding an object with an additional breakpoint property.
			$attributeVariablesObject['breakpoint'] = ($breakpointIndex === $defaultBreakpointIndex) ? 'default' : $breakpointName;
			$breakpointAttributeValues[] = $attributeVariablesObject;
		};

		return $breakpointAttributeValues;
	}

	/**
	 * Iterating through variables matching the keys from responsiveAttributes and translating it to responsive attributes names.
	 *
	 * @param array<string, mixed> $responsiveAttributes Responsive attributes that are read from component's/block's manifest.
	 * @param array<string, mixed> $variables Object containing objects with component's/block's attribute variables that are read from manifest.
	 *
	 * @return array<string, array<string, mixed>> Array prepared for setting all the variables to its breakpoints.
	 */
	private static function setupResponsiveVariables(array $responsiveAttributes, array $variables): array
	{
		$responsiveAttributesVariables = [];

		// Iterate through responsive attributes.
		foreach ($responsiveAttributes as $responsiveAttributeName => $responsiveAttributeObject) {
			// If responsive attribute doesn't exist in variables object, skip it.
			if (!$responsiveAttributeName || !isset($variables[$responsiveAttributeName])) {
				continue;
			}

			// Used for determination of default breakpoint.
			$numberOfBreakpoints = \count($responsiveAttributeObject);
			$responsiveAttribute = [];
			$breakpointIndex = 0;

			/**
			 * Iterate each responsive attribute object as breakpoint name is the
			 * key of the object, and value represents the name of the responsive variable.
			 */
			foreach ($responsiveAttributeObject as $breakpointName => $breakpointVariableName) {
				$breakpointVariables = [];

				// Determines whether array is a key value pair or not.
				$isAssociative = \array_values($variables[$responsiveAttributeName]) === $variables[$responsiveAttributeName];

				if ($isAssociative) {
					// Array represents direct value(default or value).
					$breakpointVariables = self::setBreakpointResponsiveVariables(
						$variables[$responsiveAttributeName],
						$breakpointName,
						$breakpointIndex,
						$numberOfBreakpoints
					);
				} else {
					/**
					 * Object treatment goes depending on a value inserted(multiple choice, boolean or similar).
					 * Iterate options/multiple choices/boolean...
					 */
					foreach ($variables[$responsiveAttributeName] as $attrValue => $attrObject) {
						$breakpointVariables[$attrValue] = self::setBreakpointResponsiveVariables(
							$attrObject,
							$breakpointName,
							$breakpointIndex,
							$numberOfBreakpoints
						);
					}
				}

				// Collect all the values from one responsive attribute to one associative array.
				$responsiveAttribute[$breakpointVariableName] = $breakpointVariables;
				$breakpointIndex++;
			}
			// Merge multiple responsive attributes to one array.
			$responsiveAttributesVariables = \array_merge($responsiveAttributesVariables, $responsiveAttribute);
		};

		return $responsiveAttributesVariables;
	}

	/**
	 * Extracting the names of default breakpoints depending on the case used in responsive(mobile first/desktop first).
	 * Returning the 'min' key with default name for mobile first, and the 'max' key for desktop first version.
	 * If there are no breakpoints, min and max will be empty strings.
	 *
	 * @param array<string, mixed> $breakpoints Attributes that are read from component's/block's manifest.
	 *
	 * @return array<string, mixed> Associative array with min and max keys.
	 */
	private static function getDefaultBreakpoints(array $breakpoints): array
	{
		return [
			'min' => \array_keys($breakpoints)[0] ?? '',
			'max' => \array_keys($breakpoints)[\count($breakpoints) - 1] ?? '',
		];
	}

	/**
	 * Iterating through variables matching the keys from responsiveAttributes and translating it to responsive attributes names.
	 *
	 * @param array<string, mixed> $attributes Attributes that are read from component's/block's manifest.
	 * @param array<string, array<string, mixed>> $variables Variables that are read from component's/block's manifest.
	 * @param array<int|string, mixed> $data Predefined structure for adding styles to a specific breakpoint value.
	 * @param array<string, mixed> $manifest Component/block manifest data.
	 * @param array<string, mixed> $defaultBreakpoints Default breakpoints for mobile/desktop first.
	 *
	 * @return array<int|string, mixed> Array prepared for setting all the variables to its breakpoints.
	 */
	private static function setVariablesToBreakpoints(array $attributes, array $variables, array $data, array $manifest, array $defaultBreakpoints): array
	{
		foreach ($variables as $variableName => $variableValue) {
			// Constant for attributes set value (in db or default).
			$attributeValue = $attributes[Components::getAttrKey($variableName, $attributes, $manifest)] ?? '';

			// Make sure this works correctly for attributes which are toggles (booleans).
			if (\is_bool($attributeValue)) {
				$attributeValue = $attributeValue ? 'true' : 'false';
			}

			// If type default or value.
			if (!Components::arrayIsList($variableValue)) {
				$variableValue = $variableValue[$attributeValue] ?? [];
			}

			// Bailout if wrong type is provided.
			if (!\is_array($variableValue)) {
				continue;
			}

			// Iterate variable array to check breakpoints.
			foreach ($variableValue as $breakpointItem) {
				// Define variables from breakpointItem.
				$variable = $breakpointItem['variable'] ?? [];

				// Check if we are using mobile or desktop first. Mobile first is the default.
				$isInverse = $breakpointItem['inverse'] ?? false; // If inverse is not set use mobile first.
				$type = $isInverse ? 'max' : 'min';

				// If breakpoint is not set or if breakpoint is a default breakpoint use default name.
				$isDefaultBreakpoint = empty($breakpointItem['breakpoint']) || $breakpointItem['breakpoint'] === $defaultBreakpoints[$type];
				$breakpoint = $isDefaultBreakpoint ? 'default' : $breakpointItem['breakpoint'];


				// Iterate each data array to find the correct breakpoint.
				foreach ($data as $index => $item) {
					// Check if breakpoint and type match.
					if (
						$item['name'] === $breakpoint &&
						$item['type'] === $type &&
						(
							!empty((string) $attributeValue) ||
							\gettype($attributeValue) === 'integer' ||
							\gettype($attributeValue) === 'float' ||
							\gettype($attributeValue) === 'double' ||
							$attributeValue === '0'
						)
					) {
						// Merge data variables with the new variables array.
						$data[$index]['variable'] = \array_merge($item['variable'], self::variablesInner($variable, $attributeValue));
					}
				}
			}
		}

		return $data;
	}

	/**
	 * Create initial array of data to be able to populate later.
	 *
	 * @param array<string, mixed> $globalBreakpoints Global breakpoints from global manifest to set the correct output.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	private static function prepareVariableData(array $globalBreakpoints): array
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
			$itemObjectMin = \array_merge(
				$itemObject,
				[
					'type' => 'min',
					'value' => $minBreakpointValue ?? 0,
				]
			);

			// Inner object for max values.
			$itemObjectMax = \array_merge(
				$itemObject,
				[
					'type' => 'max',
				]
			);

			// Transfer value to bigger breakpoint.
			$minBreakpointValue = $itemValue;

			// Push both min and max to the defined arrays.
			$min[] = $itemObjectMin;
			$max[] = $itemObjectMax;
		};

		// Pop largest breakpoint out of min array.
		\array_shift($min);

		// Add default object to the top of the array as minimum.
		\array_unshift(
			$min,
			[
				'type' => 'min',
				'name' => 'default',
				'value' => 0,
				'variable' => [],
			]
		);

		// Reverse order of max array.
		$max = \array_reverse($max);

		// Throw out the largest.
		\array_shift($max);

		// Switch the largest to default.
		\array_unshift(
			$max,
			[
				'type' => 'max',
				'name' => 'default',
				'value' => 0,
				'variable' => [],
			]
		);

		// Merge both arrays.
		return \array_merge($min, $max);
	}

	/**
	 * Internal helper to loop CSS Variables from array.
	 *
	 * @param array<string, mixed> $variables Array of variables of CSS variables.
	 * @param mixed $attributeValue Original attribute value used in magic variable.
	 *
	 * @return array<int, mixed>|string[]
	 */
	private static function variablesInner(array $variables, $attributeValue): array
	{
		$output = [];

		// Bailout if provided list is not an object.
		if (Components::arrayIsList($variables)) {
			return $output;
		}

		// Iterate each attribute and make corrections.
		foreach ($variables as $variableKey => $variableValue) {
			// Convert to correct case.
			$internalKey = Components::camelToKebabCase($variableKey);

			// If value contains magic variable swap that variable with original attribute value.
			if (\strpos($variableValue, '%value%') !== false) {
				$variableValue = \str_replace('%value%', (string) $attributeValue, $variableValue);
			}

			// Output the custom CSS variable by adding the attribute key + custom object key.
			$output[] = "--{$internalKey}: {$variableValue};";
		}

		return $output;
	}
}
