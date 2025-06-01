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
	 * Cache for frequently accessed config values to avoid repeated function calls.
	 *
	 * @var array<string, mixed>
	 */
	private static array $configCache = [];

	/**
	 * Cache for global variables to avoid repeated processing.
	 *
	 * @var array<string, mixed>
	 */
	private static array $globalVariablesCache = [];

	/**
	 * Cache for processed breakpoints to avoid repeated calculations.
	 *
	 * @var array<string, mixed>
	 */
	private static array $breakpointsCache = [];

	/**
	 * Cache for case conversion results to avoid repeated processing.
	 *
	 * @var array<string, string>
	 */
	private static array $caseConversionCache = [];

	/**
	 * Cache for hex to RGB conversions to avoid repeated calculations.
	 *
	 * @var array<string, string>
	 */
	private static array $hexToRgbCache = [];

	/**
	 * Get cached config value with optimized access.
	 *
	 * @param string $key Config key to retrieve.
	 *
	 * @return mixed
	 */
	protected static function getCachedConfig(string $key)
	{
		if (!isset(self::$configCache[$key])) {
			switch ($key) {
				case 'outputCssGlobally':
					self::$configCache[$key] = Helpers::getConfigOutputCssGlobally();
					break;
				case 'outputCssOptimize':
					self::$configCache[$key] = Helpers::getConfigOutputCssOptimize();
					break;
				case 'outputCssSelectorName':
					self::$configCache[$key] = Helpers::getConfigOutputCssSelectorName();
					break;
				case 'outputCssGloballyAdditionalStyles':
					self::$configCache[$key] = Helpers::getConfigOutputCssGloballyAdditionalStyles();
					break;
				case 'useLegacyComponents':
					self::$configCache[$key] = Helpers::getConfigUseLegacyComponents();
					break;
				default:
					return null;
			}
		}

		return self::$configCache[$key];
	}

	/**
	 * Get cached global variables with optimized access.
	 *
	 * @return array<string, mixed>
	 */
	protected static function getCachedGlobalVariables(): array
	{
		if (empty(self::$globalVariablesCache)) {
			self::$globalVariablesCache = Helpers::getSettingsGlobalVariables();
		}

		return self::$globalVariablesCache;
	}

	/**
	 * Get cached breakpoints with optimized processing.
	 *
	 * @param bool $sorted Whether to return sorted breakpoints.
	 *
	 * @return array<string, mixed>
	 */
	protected static function getCachedBreakpoints(bool $sorted = false): array
	{
		$cacheKey = $sorted ? 'sorted' : 'original';
		
		if (!isset(self::$breakpointsCache[$cacheKey])) {
			$breakpoints = Helpers::getSettingsGlobalVariablesBreakpoints();
			
			if ($sorted) {
				\asort($breakpoints);
			}
			
			self::$breakpointsCache[$cacheKey] = $breakpoints;
		}

		return self::$breakpointsCache[$cacheKey];
	}

	/**
	 * Optimized case conversion with caching.
	 *
	 * @param string $input String to convert.
	 *
	 * @return string
	 */
	protected static function camelToKebabCaseCached(string $input): string
	{
		if (!isset(self::$caseConversionCache[$input])) {
			self::$caseConversionCache[$input] = Helpers::camelToKebabCase($input);
		}

		return self::$caseConversionCache[$input];
	}

	/**
	 * Get Global Manifest.json and return globalVariables as CSS variables with optimized processing.
	 *
	 * @return string
	 */
	public static function outputCssVariablesGlobal(): string
	{
		$globalVariables = self::getCachedGlobalVariables();
		
		// Early return for empty global variables
		if (empty($globalVariables)) {
			return '';
		}

		$output = '';

		foreach ($globalVariables as $itemKey => $itemValue) {
			$itemKey = self::camelToKebabCaseCached($itemKey);

			if (\is_array($itemValue)) {
				$output .= self::globalInner($itemValue, $itemKey);
			} else {
				$output .= "--global-{$itemKey}: {$itemValue};\n";
			}
		}

		// Early return for empty output
		if ($output === '') {
			return '';
		}

		$id = self::getCachedConfig('outputCssSelectorName');
		$outputWrapped = "<style id='{$id}-global'>:root {{$output}}</style>";

		if (self::getCachedConfig('outputCssOptimize')) {
			$outputWrapped = \str_replace(["\n", "\r"], '', $outputWrapped);
		}

		return $outputWrapped;
	}

	/**
	 * Get component/block options and process them in CSS variables with optimized performance.
	 *
	 * @param array<string, mixed> $attributes Built attributes.
	 * @param array<string, mixed> $manifest Component/block manifest data.
	 * @param string $unique Unique key.
	 * @param string $customSelector Output custom selector to use as a style prefix.
	 *
	 * @return string
	 */
	public static function outputCssVariables(array $attributes, array $manifest, string $unique, string $customSelector = ''): string
	{
		// Early return if manifest is missing variables key
		if (!isset($manifest['variables']) && !isset($manifest['variablesCustom'])) {
			return '';
		}

		// Get cached breakpoints (sorted)
		$breakpoints = self::getCachedBreakpoints(true);
		$defaultBreakpoints = self::getDefaultBreakpoints($breakpoints);

		// Define variables from manifest
		$variables = $manifest['variables'] ?? [];
		$responsiveAttributes = $manifest['responsiveAttributes'] ?? [];

		// Get the initial data array
		$data = self::prepareVariableData($breakpoints);

		// Check if component or block
		$name = $manifest['componentClass'] ?? $attributes['blockClass'];

		if ($customSelector !== '') {
			$name = $customSelector;
		}

		// Optimize default attributes processing
		$attributes = self::processDefaultAttributes($attributes, $variables);

		// Process responsive variables if they exist
		if (!empty($responsiveAttributes)) {
			$responsiveVariables = self::setupResponsiveVariables($responsiveAttributes, $variables);
			$data = self::setVariablesToBreakpoints($attributes, $responsiveVariables, $data, $manifest, $defaultBreakpoints);
		}

		// Process regular variables if they exist
		if (!empty($variables)) {
			$data = self::setVariablesToBreakpoints($attributes, $variables, $data, $manifest, $defaultBreakpoints);
		}

		// Optimized context checking
		$context = self::getRequestContext();
		$outputGlobally = self::getCachedConfig('outputCssGlobally');

		// If default output just echo
		if (!$outputGlobally || (\wp_is_json_request() && $context === 'edit')) {
			return self::getCssVariablesTypeDefault($name, $data, $manifest, $unique);
		}

		// Set inline styles
		$inlineStyle = self::getCssVariablesTypeInline($name, $data, $manifest, $unique);
		if (!empty($inlineStyle)) {
			Helpers::setStyle($inlineStyle);
		}

		return '';
	}

	/**
	 * Process default attributes efficiently.
	 *
	 * @param array<string, mixed> $attributes Original attributes.
	 * @param array<string, mixed> $variables Variables from manifest.
	 *
	 * @return array<string, mixed>
	 */
	protected static function processDefaultAttributes(array $attributes, array $variables): array
	{
		// Early return if no variables
		if (empty($variables)) {
			return $attributes;
		}

		// Get missing attributes with variable key efficiently
		$defaultAttributes = [];
		
		foreach ($variables as $key => $value) {
			if (!isset($attributes[$key]) && isset($attributes[$key]['default'])) {
				$defaultAttributes[$key] = $attributes[$key]['default'];
			}
		}

		// Merge defaults if any were found
		return empty($defaultAttributes) ? $attributes : \array_merge($defaultAttributes, $attributes);
	}

	/**
	 * Get request context efficiently.
	 *
	 * @return string
	 */
	protected static function getRequestContext(): string
	{
		static $context = null;
		
		if ($context === null) {
			$context = isset($_GET['context']) ? \sanitize_text_field(\wp_unslash($_GET['context'])) : '';
		}
		
		return $context;
	}

	/**
	 * Output css variables as a one inline style tag with optimized processing.
	 *
	 * @return string
	 */
	public static function outputCssVariablesInline(): string
	{
		// Optimized context checking
		$context = self::getRequestContext();
		$outputGlobally = self::getCachedConfig('outputCssGlobally');

		// If default output just return empty
		if (!$outputGlobally || (\wp_is_json_request() && $context === 'edit')) {
			return '';
		}

		$styles = Helpers::getStyles();

		// Early return if styles are missing
		if (empty($styles)) {
			return '';
		}

		// Get cached breakpoints data (sorted)
		$breakpointsData = self::getCachedBreakpoints(true);

		// Prepare breakpoints array efficiently
		$breakpoints = self::prepareInlineBreakpoints($breakpointsData);

		// Process styles efficiently
		$breakpoints = self::processStylesForInline($styles, $breakpoints);

		// Generate output efficiently
		$output = self::generateInlineOutput($breakpoints);

		// Get additional styles
		$additionalStyles = self::getCachedConfig('outputCssGloballyAdditionalStyles');
		$additionalStylesOutput = !empty($additionalStyles) ? \esc_html(\implode(";\n", $additionalStyles)) : '';

		$selector = self::getCachedConfig('outputCssSelectorName');

		return "<style id='{$selector}'>{$output} {$additionalStylesOutput}</style>";
	}

	/**
	 * Prepare breakpoints for inline processing efficiently.
	 *
	 * @param array<string, mixed> $breakpointsData Breakpoints data.
	 *
	 * @return array<string, string>
	 */
	protected static function prepareInlineBreakpoints(array $breakpointsData): array
	{
		// Populate min values efficiently
		$breakpointsMin = \array_map(
			static function ($item) {
				return "min---{$item}";
			},
			\array_values($breakpointsData)
		);
		\array_unshift($breakpointsMin, 'min---0');

		// Populate max values efficiently
		$breakpointsMax = \array_map(
			static function ($item) {
				return "max---{$item}";
			},
			\array_reverse(\array_values($breakpointsData))
		);
		\array_unshift($breakpointsMax, 'max---0');

		// Create empty breakpoints array efficiently
		$allBreakpoints = \array_merge($breakpointsMin, $breakpointsMax);
		return \array_fill_keys($allBreakpoints, '');
	}

	/**
	 * Process styles for inline output efficiently.
	 *
	 * @param array<mixed> $styles Styles to process.
	 * @param array<string, string> $breakpoints Breakpoints array.
	 *
	 * @return array<string, string>
	 */
	protected static function processStylesForInline(array $styles, array $breakpoints): array
	{
		foreach ($styles as $style) {
			$name = $style['name'] ?? '';
			$unique = $style['unique'] ?? '';
			$variables = $style['variables'] ?? [];

			// Skip if variables are missing
			if (empty($variables)) {
				continue;
			}

			$uniqueSelector = $unique ? "[data-id='{$unique}']" : '';

			foreach ($variables as $data) {
				$type = $data['type'] ?? '';
				$value = $data['value'] ?? '';
				$variable = $data['variable'] ?? '';

				// Skip if variable is missing
				if ($variable === '') {
					continue;
				}

				$breakpointKey = "{$type}---{$value}";

				// Skip if breakpoint is missing
				if (!isset($breakpoints[$breakpointKey])) {
					continue;
				}

				// Append to breakpoint efficiently
				$breakpoints[$breakpointKey] .= "\n.{$name}{$uniqueSelector}{\n{$variable}\n} ";
			}
		}

		return $breakpoints;
	}

	/**
	 * Generate inline output efficiently.
	 *
	 * @param array<string, string> $breakpoints Processed breakpoints.
	 *
	 * @return string
	 */
	protected static function generateInlineOutput(array $breakpoints): string
	{
		$output = '';
		$optimize = self::getCachedConfig('outputCssOptimize');

		foreach ($breakpoints as $breakpointKey => $breakpointValue) {
			// Skip empty values
			if ($breakpointValue === '') {
				continue;
			}

			$breakpointParts = \explode('---', $breakpointKey, 2);
			$type = $breakpointParts[0] ?? '';
			$value = $breakpointParts[1] ?? '';

			// If value is 0 then this breakpoint has no media query
			if ($value === '0') {
				$output .= "{$breakpointValue}\n";
			} else {
				$output .= "\n@media ({$type}-width:{$value}px){{$breakpointValue}}\n ";
			}
		}

		// Optimize output if needed
		if ($optimize) {
			$output = \str_replace(["\n", "\r"], '', $output);
		}

		return $output;
	}

	/**
	 * Convert a hex color into RGB values with optimized caching.
	 *
	 * @param string $hex Input hex color.
	 *
	 * @return string
	 */
	public static function hexToRgb(string $hex): string
	{
		// Early return for empty hex
		if ($hex === '') {
			return '0 0 0';
		}

		// Check cache first
		if (isset(self::$hexToRgbCache[$hex])) {
			return self::$hexToRgbCache[$hex];
		}

		// Remove the # at the beginning and filter out invalid hex characters
		$cleanHex = \preg_replace("/[^0-9A-Fa-f]/", '', $hex);
		$length = \strlen($cleanHex);

		if ($length === 3) {
			$r = \hexdec(\str_repeat(\substr($cleanHex, 0, 1), 2));
			$g = \hexdec(\str_repeat(\substr($cleanHex, 1, 1), 2));
			$b = \hexdec(\str_repeat(\substr($cleanHex, 2, 1), 2));
		} elseif ($length === 6) {
			$r = \hexdec(\substr($cleanHex, 0, 2));
			$g = \hexdec(\substr($cleanHex, 2, 2));
			$b = \hexdec(\substr($cleanHex, 4, 2));
		} else {
			$r = $g = $b = 0;
		}

		$result = "{$r} {$g} {$b}";

		// Cache result (limit cache size)
		if (\count(self::$hexToRgbCache) < 100) {
			self::$hexToRgbCache[$hex] = $result;
		}

		return $result;
	}

	/**
	 * Return unique ID for block processing.
	 *
	 * @return string
	 */
	public static function getUnique(): string
	{
		return wp_unique_id('es-');
	}

	/**
	 * Return CSS variables in default type with optimized processing.
	 *
	 * @param string $name Output css selector name.
	 * @param array<mixed> $data Data prepared for checking.
	 * @param array<mixed> $manifest Component/block manifest data.
	 * @param string $unique Unique key.
	 *
	 * @return string
	 */
	protected static function getCssVariablesTypeDefault(string $name, array $data, array $manifest, string $unique): string
	{
		$output = '';
		$uniqueSelector = $unique ? "[data-id='{$unique}']" : '';

		// Process data efficiently
		foreach ($data as $values) {
			$type = $values['type'] ?? '';
			$value = $values['value'] ?? 0;
			$variable = $values['variable'] ?? [];

			// Skip if variables are empty
			if (empty($variable)) {
				continue;
			}

			// Merge array of variables to string efficiently
			$breakpointData = \implode("\n", $variable);

			// Build output based on breakpoint value
			if ($value === 0) {
				$output .= "\n .{$name}{$uniqueSelector}{\n{$breakpointData}\n}";
			} else {
				$output .= "\n @media ({$type}-width:{$value}px){\n.{$name}{$uniqueSelector}{\n{$breakpointData}\n}\n}";
			}
		}

		// Process manual output efficiently
		$manual = isset($manifest['variablesCustom']) ? \esc_html(\implode(";\n", $manifest['variablesCustom'])) : '';

		// Early return check
		if ($output === '' && $manual === '') {
			return '';
		}

		$finalManualOutput = $manual ? "\n .{$name}{$uniqueSelector}{\n{$manual}\n}" : '';

		// Optimize if needed
		if (self::getCachedConfig('outputCssOptimize')) {
			$output = \str_replace(["\n", "\r"], '', $output);
			$finalManualOutput = \str_replace(["\n", "\r"], '', $finalManualOutput);
		}

		return "<style>{$output} {$finalManualOutput}</style>";
	}

	/**
	 * Get css variables in inline type with optimized processing.
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
		$styles = [
			'name' => $name,
			'unique' => $unique,
			'variables' => [],
		];

		// Process data efficiently
		foreach ($data as $values) {
			$type = $values['type'] ?? '';
			$value = $values['value'] ?? 0;
			$variable = $values['variable'] ?? [];

			// Skip if variables are empty
			if (empty($variable)) {
				continue;
			}

			$breakpointData = \implode("\n", $variable);

			$styles['variables'][] = [
				'type' => $type,
				'variable' => $breakpointData,
				'value' => $value,
			];
		}

		// Process manual variables
		$manual = isset($manifest['variablesCustom']) ? \esc_html(\implode(";\n", $manifest['variablesCustom'])) : '';

		if ($manual !== '') {
			$styles['variables'][] = [
				'type' => 'min',
				'variable' => $manual,
				'value' => 0,
			];
		}

		// Return empty array if no styles were added
		return empty($styles['variables']) ? [] : $styles;
	}

	/**
	 * Process and return global css variables based on the type with optimized processing.
	 *
	 * @param array<string, mixed> $itemValues Values of data to check.
	 * @param string $itemKey Item key to check.
	 *
	 * @return string
	 */
	private static function globalInner(array $itemValues, string $itemKey): string
	{
		// Early return for empty values
		if (empty($itemValues)) {
			return '';
		}

		$output = '';

		foreach ($itemValues as $key => $value) {
			$key = self::camelToKebabCaseCached((string)$key);
			$itemKey = self::camelToKebabCaseCached((string)$itemKey);

			switch ($itemKey) {
				case 'colors':
					if (isset($value['slug'], $value['color'])) {
						$output .= "--global-{$itemKey}-{$value['slug']}: {$value['color']};\n";
						$rgbValues = self::hexToRgb($value['color']);
						$output .= "--global-{$itemKey}-{$value['slug']}-values: {$rgbValues};\n";
					}
					break;
				case 'gradients':
					if (isset($value['slug'], $value['gradient'])) {
						$output .= "--global-{$itemKey}-{$value['slug']}: {$value['gradient']};\n";
					}
					break;
				case 'font-sizes':
					if (isset($value['slug'])) {
						$output .= "--global-{$itemKey}-{$value['slug']}: {$value['slug']};\n";
					}
					break;
				default:
					$output .= "--global-{$itemKey}-{$key}: {$value};\n";
					break;
			}
		}

		return $output;
	}

	/**
	 * Sets up a breakpoint value to responsive attribute objects with optimized processing.
	 *
	 * @param array<string, mixed> $attributeVariables Array of attribute variables object.
	 * @param string $breakpointName Breakpoint name from responsiveAttribute's breakpoint in block's/component's manifest.
	 * @param integer $breakpointIndex Index of responsiveAttribute's breakpoint in manifest.
	 * @param integer $numberOfBreakpoints Number of responsiveAttribute breakpoints in block's/component's manifest.
	 *
	 * @return array<int, mixed>
	 */
	protected static function setBreakpointResponsiveVariables(
		array $attributeVariables,
		string $breakpointName,
		int $breakpointIndex,
		int $numberOfBreakpoints
	): array {
		$breakpointAttributeValues = [];
		
		foreach ($attributeVariables as $attributeVariablesObject) {
			// Calculate default breakpoint index efficiently
			$defaultBreakpointIndex = (isset($attributeVariablesObject['inverse']) && $attributeVariablesObject['inverse']) ? 0 : ($numberOfBreakpoints - 1);

			// Add breakpoint property efficiently
			$attributeVariablesObject['breakpoint'] = ($breakpointIndex === $defaultBreakpointIndex) ? 'default' : $breakpointName;
			$breakpointAttributeValues[] = $attributeVariablesObject;
		}

		return $breakpointAttributeValues;
	}

	/**
	 * Setup responsive variables with optimized processing.
	 *
	 * @param array<string, mixed> $responsiveAttributes Responsive attributes that are read from component's/block's manifest.
	 * @param array<string, mixed> $variables Object containing objects with component's/block's attribute variables that are read from manifest.
	 *
	 * @return array<string, array<string, mixed>> Array prepared for setting all the variables to its breakpoints.
	 */
	protected static function setupResponsiveVariables(array $responsiveAttributes, array $variables): array
	{
		$responsiveAttributesVariables = [];

		foreach ($responsiveAttributes as $responsiveAttributeName => $responsiveAttributeObject) {
			// Skip if responsive attribute doesn't exist in variables
			if (!$responsiveAttributeName || !isset($variables[$responsiveAttributeName])) {
				continue;
			}

			$numberOfBreakpoints = \count($responsiveAttributeObject);
			$responsiveAttribute = [];
			$breakpointIndex = 0;

			foreach ($responsiveAttributeObject as $breakpointName => $breakpointVariableName) {
				$breakpointVariables = [];

				// Check if array is associative efficiently
				$isAssociative = \array_is_list($variables[$responsiveAttributeName]);

				if ($isAssociative) {
					$breakpointVariables = self::setBreakpointResponsiveVariables(
						$variables[$responsiveAttributeName],
						$breakpointName,
						$breakpointIndex,
						$numberOfBreakpoints
					);
				} else {
					foreach ($variables[$responsiveAttributeName] as $attrValue => $attrObject) {
						$breakpointVariables[$attrValue] = self::setBreakpointResponsiveVariables(
							$attrObject,
							$breakpointName,
							$breakpointIndex,
							$numberOfBreakpoints
						);
					}
				}

				$responsiveAttribute[$breakpointVariableName] = $breakpointVariables;
				$breakpointIndex++;
			}
			
			$responsiveAttributesVariables = \array_merge($responsiveAttributesVariables, $responsiveAttribute);
		}

		return $responsiveAttributesVariables;
	}

	/**
	 * Get default breakpoints with optimized processing.
	 *
	 * @param array<string, mixed> $breakpoints Attributes that are read from component's/block's manifest.
	 *
	 * @return array<string, mixed> Associative array with min and max keys.
	 */
	protected static function getDefaultBreakpoints(array $breakpoints): array
	{
		if (empty($breakpoints)) {
			return ['min' => '', 'max' => ''];
		}

		$keys = \array_keys($breakpoints);
		return [
			'min' => $keys[0],
			'max' => $keys[\count($keys) - 1],
		];
	}

	/**
	 * Set variables to breakpoints with optimized processing.
	 *
	 * @param array<string, mixed> $attributes Attributes that are read from component's/block's manifest.
	 * @param array<string, array<string, mixed>> $variables Variables that are read from component's/block's manifest.
	 * @param array<int|string, mixed> $data Predefined structure for adding styles to a specific breakpoint value.
	 * @param array<string, mixed> $manifest Component/block manifest data.
	 * @param array<string, mixed> $defaultBreakpoints Default breakpoints for mobile/desktop first.
	 *
	 * @return array<int|string, mixed> Array prepared for setting all the variables to its breakpoints.
	 */
	protected static function setVariablesToBreakpoints(array $attributes, array $variables, array $data, array $manifest, array $defaultBreakpoints): array
	{
		foreach ($variables as $variableName => $variableValue) {
			$attributeKey = Helpers::getAttrKey($variableName, $attributes, $manifest);
			$attributeValue = $attributes[$attributeKey] ?? '';

			// Handle boolean attributes efficiently
			if (\is_bool($attributeValue)) {
				$attributeValue = $attributeValue ? 'true' : 'false';
			}

			// Handle default or value type
			if (!\is_array($variableValue) || Helpers::arrayIsList($variableValue)) {
				continue;
			}

			$variableValue = $variableValue[$attributeValue] ?? [];

			// Skip if wrong type provided
			if (!\is_array($variableValue)) {
				continue;
			}

			// Process breakpoint items efficiently
			foreach ($variableValue as $breakpointItem) {
				$variable = $breakpointItem['variable'] ?? [];
				
				if (empty($variable)) {
					continue;
				}

				$isInverse = $breakpointItem['inverse'] ?? false;
				$type = $isInverse ? 'max' : 'min';
				$isDefaultBreakpoint = empty($breakpointItem['breakpoint']) || $breakpointItem['breakpoint'] === $defaultBreakpoints[$type];
				$breakpoint = $isDefaultBreakpoint ? 'default' : $breakpointItem['breakpoint'];

				// Check if attribute value is valid
				if (self::isValidAttributeValue($attributeValue)) {
					self::addVariableToData($data, $breakpoint, $type, $variable, $attributeValue, $attributes, $manifest);
				}
			}
		}

		return $data;
	}

	/**
	 * Check if attribute value is valid for processing.
	 *
	 * @param mixed $attributeValue Attribute value to check.
	 *
	 * @return bool
	 */
	protected static function isValidAttributeValue($attributeValue): bool
	{
		return !empty((string) $attributeValue) ||
			   \is_int($attributeValue) ||
			   \is_float($attributeValue) ||
			   $attributeValue === '0';
	}

	/**
	 * Add variable to data array efficiently.
	 *
	 * @param array<int|string, mixed> $data Data array reference.
	 * @param string $breakpoint Breakpoint name.
	 * @param string $type Breakpoint type.
	 * @param array<string, mixed> $variable Variable data.
	 * @param mixed $attributeValue Attribute value.
	 * @param array<string, mixed> $attributes All attributes.
	 * @param array<string, mixed> $manifest Manifest data.
	 *
	 * @return void
	 */
	protected static function addVariableToData(array &$data, string $breakpoint, string $type, array $variable, $attributeValue, array $attributes, array $manifest): void
	{
		foreach ($data as $index => $item) {
			if ($item['name'] === $breakpoint && $item['type'] === $type) {
				$data[$index]['variable'] = \array_merge(
					$item['variable'],
					self::variablesInner($variable, $attributeValue, $attributes, $manifest)
				);
				break;
			}
		}
	}

	/**
	 * Create initial array of data with optimized processing.
	 *
	 * @param array<string, mixed> $globalBreakpoints Global breakpoints from global manifest to set the correct output.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	protected static function prepareVariableData(array $globalBreakpoints): array
	{
		$min = [];
		$max = [];
		$minBreakpointValue = 0;

		// Process breakpoints efficiently
		foreach ($globalBreakpoints as $itemKey => $itemValue) {
			$baseObject = [
				'name' => $itemKey,
				'value' => $itemValue,
				'variable' => [],
			];

			$min[] = \array_merge($baseObject, [
				'type' => 'min',
				'value' => $minBreakpointValue,
			]);

			$max[] = \array_merge($baseObject, [
				'type' => 'max',
			]);

			$minBreakpointValue = $itemValue;
		}

		// Process min array
		\array_shift($min);
		\array_unshift($min, [
			'type' => 'min',
			'name' => 'default',
			'value' => 0,
			'variable' => [],
		]);

		// Process max array
		$max = \array_reverse($max);
		\array_shift($max);
		\array_unshift($max, [
			'type' => 'max',
			'name' => 'default',
			'value' => 0,
			'variable' => [],
		]);

		return \array_merge($min, $max);
	}

	/**
	 * Internal helper to loop CSS Variables from array with optimized processing.
	 *
	 * @param array<string, mixed> $variables Array of variables of CSS variables.
	 * @param mixed $attributeValue Original attribute value used in magic variable.
	 * @param array<string, mixed> $attributes Attributes that are read from component's/block's manifest.
	 * @param array<string, mixed> $manifest Component/block manifest data.
	 *
	 * @return array<int, mixed>|string[]
	 */
	protected static function variablesInner(array $variables, $attributeValue, array $attributes, array $manifest): array
	{
		// Early return if provided list is not an object
		if (Helpers::arrayIsList($variables)) {
			return [];
		}

		$output = [];
		$attributeValueString = (string) $attributeValue;

		foreach ($variables as $variableKey => $variableValue) {
			$internalKey = self::camelToKebabCaseCached($variableKey);

			// Process magic variable replacement efficiently
			if (\str_contains($variableValue, '%value%')) {
				$variableValue = \str_replace('%value%', $attributeValueString, $variableValue);
			}

			// Process attribute replacements efficiently
			$variableValue = self::processAttributeReplacements($variableValue, $attributes, $manifest);

			$output[] = "--{$internalKey}: {$variableValue};";
		}

		return $output;
	}

	/**
	 * Process attribute replacements efficiently.
	 *
	 * @param string $variableValue Variable value to process.
	 * @param array<string, mixed> $attributes All attributes.
	 * @param array<string, mixed> $manifest Manifest data.
	 *
	 * @return string
	 */
	protected static function processAttributeReplacements(string $variableValue, array $attributes, array $manifest): string
	{
		$useLegacy = self::getCachedConfig('useLegacyComponents');
		$componentName = $useLegacy ? ($manifest['componentName'] ?? '') : ($manifest['blockName'] ?? '');

		foreach ($attributes as $attrKey => $attrValue) {
			$key = isset($attributes['prefix']) 
				? \str_replace($attributes['prefix'], Helpers::kebabToCamelCase($componentName), $attrKey)
				: $attrKey;

			$placeholder = "%attr-{$key}%";
			if (\str_contains($variableValue, $placeholder)) {
				$variableValue = \str_replace($placeholder, (string) $attrValue, $variableValue);
			}
		}

		return $variableValue;
	}
}
