<?php

/**
 * Helpers for attributes.
 *
 * @package EightshiftLibs\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Helpers;

use Exception;

/**
 * Class AttributesTrait Helper
 */
trait AttributesTrait
{
	/**
	 * Check if attribute exist in attributes list and add default value if not.
	 * This is used because Block editor will not output attributes that don't have a default value.
	 *
	 * @param string $key Key to check.
	 * @param array<string, mixed> $attributes Array of attributes.
	 * @param array<string, mixed> $manifest Array of default attributes from manifest.json.
	 * @param bool $undefinedAllowed Allowed detection of undefined values.
	 *
	 * @throws Exception When we're unable to find the component by $component.
	 *
	 * @return mixed
	 */
	public static function checkAttr(string $key, array $attributes, array $manifest, bool $undefinedAllowed = false)
	{
		// Fast path: Check if the original key exists first (most common case).
		if (isset($attributes[$key])) {
			return $attributes[$key];
		}

		// Cache manifest attributes to avoid repeated array access.
		$manifestAttrs = $manifest['attributes'] ?? null;
		if ($manifestAttrs === null) {
			// Handle missing attributes array case.
			$contextName = $manifest['blockName'] ?? $manifest['componentName'] ?? 'unknown';
			$contextType = isset($manifest['blockName']) ? 'block' : 'component';
			throw new Exception("{$key} key does not exist - missing attributes in {$contextName} {$contextType} manifest.");
		}

		// Only compute the transformed key if the original key wasn't found.
		$newKey = $key; // Default to original key.

		// Only call getAttrKey if we're in a component context and need prefix transformation.
		if (
			!isset($manifest['blockName']) &&
			!\str_contains($key, 'wrapper') &&
			!empty($attributes['prefix'])
		) {
			$newKey = \str_replace(
				Helpers::kebabToCamelCase($manifest['componentName'] ?? ''),
				$attributes['prefix'],
				$key
			);
		}

		// Check transformed key if different from original.
		if ($newKey !== $key && isset($attributes[$newKey])) {
			return $attributes[$newKey];
		}

		// Cache manifest key to avoid repeated access.
		$manifestKey = $manifestAttrs[$key] ?? null;
		if ($manifestKey === null) {
			$contextName = $manifest['blockName'] ?? $manifest['componentName'] ?? 'unknown';
			$contextType = isset($manifest['blockName']) ? 'block' : 'component';
			$tipOutput = isset($manifest['components']) ?
				' If you are using additional components, check if you used the correct block/component prefix in your attribute name.' : '';
			throw new Exception("{$key} key does not exist in the {$contextName} {$contextType} manifest. Please check your implementation.{$tipOutput}");
		}

		// Early return for undefined allowed case.
		if ($undefinedAllowed && empty($manifestKey['default'])) {
			return null;
		}

		// Optimized default value assignment - avoid switch statement overhead.
		$default = $manifestKey['default'] ?? null;
		if ($default !== null) {
			return $default;
		}

		// Fallback defaults based on type (only when no default is specified).
		$type = $manifestKey['type'] ?? 'string';
		return match ($type) {
			'boolean' => false,
			'array', 'object' => [],
			default => ''
		};
	}

	/**
	 * Map and check attributes for responsive object.
	 *
	 * @param string $keyName Key name to find in the responsiveAttributes object.
	 * @param array<string, mixed> $attributes Array of attributes.
	 * @param array<string, mixed> $manifest Array of default attributes from manifest.json.
	 * @param bool $undefinedAllowed Allowed detection of undefined values.
	 *
	 * @throws Exception If missing responsiveAttributes or keyName in responsiveAttributes.
	 * @throws Exception If missing keyName in responsiveAttributes.
	 *
	 * @return array<mixed>
	 */
	public static function checkAttrResponsive(string $keyName, array $attributes, array $manifest, bool $undefinedAllowed = false): array
	{
		// Cache responsive attributes to avoid repeated array access.
		$responsiveAttrs = $manifest['responsiveAttributes'] ?? null;
		if ($responsiveAttrs === null) {
			$contextName = $manifest['blockName'] ?? $manifest['componentName'] ?? 'unknown';
			$contextType = isset($manifest['blockName']) ? 'block' : 'component';
			throw new Exception("It looks like you are missing responsiveAttributes key in your {$contextName} {$contextType} manifest.");
		}

		// Cache the specific keyName array to avoid repeated lookups.
		$keyConfig = $responsiveAttrs[$keyName] ?? null;
		if ($keyConfig === null) {
			throw new Exception("It looks like you are missing the {$keyName} key in your manifest responsiveAttributes array.");
		}

		// Pre-allocate output array with known size for better memory performance.
		$output = [];

		// Batch process all responsive attributes.
		foreach ($keyConfig as $key => $value) {
			$output[$key] = self::checkAttr($value, $attributes, $manifest, $undefinedAllowed);
		}

		return $output;
	}

	/**
	 * Check if the attribute's key has a prefix and output the correct attribute name.
	 *
	 * @param string $key Key to check.
	 * @param array<string, mixed> $attributes Array of attributes.
	 * @param array<string, mixed> $manifest Components/blocks manifest.json.
	 *
	 * @return string
	 */
	public static function getAttrKey(string $key, array $attributes, array $manifest): string
	{
		// Fast path: Most common cases first.

		// Skip if using this helper in block (most common case).
		if (isset($manifest['blockName'])) {
			return $key;
		}

		// Skip if attribute is wrapper (use modern PHP function).
		if (\str_contains($key, 'wrapper')) {
			return $key;
		}

		// Cache prefix to avoid repeated array access.
		$prefix = $attributes['prefix'] ?? '';
		if ($prefix === '') {
			return $key;
		}

		// Cache component name to avoid repeated array access.
		$componentName = $manifest['componentName'] ?? '';
		if ($componentName === '') {
			return $key;
		}

		// Only compute kebab-to-camel conversion if we actually need it.
		return \str_replace(Helpers::kebabToCamelCase($componentName), $prefix, $key);
	}

	/**
	 * Output only attributes that are used in the component and remove everything else.
	 *
	 * @param string $newName *New* key to use to rename attributes.
	 * @param array<string, mixed> $attributes Attributes from the block/component.
	 * @param array<string, mixed> $manual Array of attributes to change key and merge to the original output.
	 *
	 * @return array<string, mixed>
	 */
	public static function props(string $newName, array $attributes, array $manual = []): array
	{
		// Cache flipped includes array for O(1) lookup instead of O(n) in_array.
		static $includesFlipped = null;
		if ($includesFlipped === null) {
			$includes = [
				'blockName',
				'blockClientId',
				'blockTopLevelId',
				'blockFullName',
				'blockClass',
				'blockJsClass',
				'blockStyles',
				'blockSsr',
				'componentJsClass',
				'selectorClass',
				'additionalClass',
				'uniqueWrapperId',
				'parentClass'
			];
			$includesFlipped = \array_flip($includes);
		}

		$output = [];

		// Cache frequently accessed values.
		$blockName = $attributes['blockName'] ?? '';
		$attributesPrefix = $attributes['prefix'] ?? null;

		// Compute prefix once and cache kebab-to-camel conversions.
		$newNameCamel = Helpers::kebabToCamelCase($newName);

		if ($attributesPrefix === null) {
			$prefix = $blockName ? Helpers::kebabToCamelCase($blockName) : '';
		} else {
			$prefix = $attributesPrefix;
		}

		// Set component prefix.
		$output['prefix'] = empty($prefix) ? $newNameCamel : $prefix . \ucfirst($newNameCamel);

		// Cache prefix length for substr comparison optimization.
		$prefixLength = \strlen($output['prefix']);

		// Process main attributes in a single optimized loop.
		foreach ($attributes as $key => $value) {
			// Fast lookup for includes using array key existence (O(1) vs O(n)).
			if (isset($includesFlipped[$key])) {
				$output[$key] = $value;
			} elseif ($prefixLength > 0 && \str_starts_with($key, $output['prefix'])) {
				// Use modern PHP str_starts_with for better performance.
				$output[$key] = $value;
			}
		}

		// Process manual attributes if present.
		if ($manual) {
			// Cache the component name pattern for string replacement.
			$componentPattern = \lcfirst($newNameCamel);

			foreach ($manual as $key => $value) {
				if (isset($includesFlipped[$key])) {
					$output[$key] = $value;
				} else {
					// Optimize string replacement - only do it once.
					$newKey = \str_replace($componentPattern, '', $key);
					$transformedKey = $output['prefix'] . \ucfirst($newKey);
					$output[$transformedKey] = $value;
				}
			}
		}

		return $output;
	}

	/**
	 * Merges attributes array with the manifest default attributes.
	 *
	 * @param array<string, mixed> $manifest Block/Component manifest data.
	 * @param array<string, mixed> $attributes Block/Component rendered attributes data.
	 *
	 * @return array<string, mixed>
	 */
	public static function getDefaultRenderAttributes(array $manifest, array $attributes): array
	{
		// Cache manifest attributes to avoid repeated array access.
		$attrs = $manifest['attributes'] ?? null;

		// Early return for empty or invalid attributes.
		if ($attrs === null || !\is_iterable($attrs) || empty($attrs)) {
			return $attributes;
		}

		// Pre-allocate with estimated size for better memory performance.
		$defaultAttributes = [];

		// Determine if we need key transformation (only for components with prefix).
		$needsKeyTransformation = !isset($manifest['blockName']) &&
			!empty($attributes['prefix']) &&
			!empty($manifest['componentName']);

		// Cache values for key transformation if needed.
		$componentNameCamel = null;
		$prefix = null;
		if ($needsKeyTransformation) {
			$componentNameCamel = Helpers::kebabToCamelCase($manifest['componentName']);
			$prefix = $attributes['prefix'];
		}

		// Process attributes in a single optimized loop.
		foreach ($attrs as $itemKey => $itemValue) {
			// Skip if no default value is set.
			if (!isset($itemValue['default'])) {
				continue;
			}

			// Optimize key transformation.
			if ($needsKeyTransformation && !\str_contains($itemKey, 'wrapper')) {
				// Apply transformation directly without function call.
				$newKey = \str_replace($componentNameCamel, $prefix, $itemKey);
			} else {
				// Use original key (block context or no transformation needed).
				$newKey = $itemKey;
			}

			$defaultAttributes[$newKey] = $itemValue['default'];
		}

		// Merge defaults with provided attributes (provided attributes take precedence).
		return \array_merge($defaultAttributes, $attributes);
	}

	/**
	 * Get html attrs output.
	 *
	 * @param array<string, string> $attrs Array of attributes.
	 * @param bool $escape Escape the attributes.
	 *
	 * @return string
	 */
	public static function getAttrsOutput(array $attrs, bool $escape = true): string
	{
		$htmlAttrs = '';

		foreach ($attrs as $key => $value) {
			if ($escape) {
				$value = \esc_attr($value);
				$key = \esc_attr($key);
			}

			if ($value == 0 || !empty($value)) { // intentional loose comparison to allow 0 values.
				$htmlAttrs .= " {$key}='{$value}'";
				continue;
			}

			$htmlAttrs .= " {$key}";
		}

		return $htmlAttrs;
	}
}
