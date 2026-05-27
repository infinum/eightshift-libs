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
	 * Attribute keys that are always copied through `props()` regardless of prefix matching.
	 *
	 * @var array<string, true>
	 */
	private const PROPS_PASSTHROUGH_KEYS = [
		'blockName' => true,
		'blockClientId' => true,
		'blockTopLevelId' => true,
		'blockFullName' => true,
		'blockClass' => true,
		'blockJsClass' => true,
		'blockStyles' => true,
		'blockSsr' => true,
		'componentJsClass' => true,
		'selectorClass' => true,
		'additionalClass' => true,
		'uniqueWrapperId' => true,
		'parentClass' => true,
	];

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
	 */
	public static function checkAttr(string $key, array $attributes, array $manifest, bool $undefinedAllowed = false): mixed
	{
		if (isset($attributes[$key])) {
			return $attributes[$key];
		}

		$manifestAttrs = $manifest['attributes'] ?? null;
		if ($manifestAttrs === null) {
			[$contextName, $contextType] = self::manifestContext($manifest);
			throw new Exception("{$key} key does not exist - missing attributes in {$contextName} {$contextType} manifest.");
		}

		$newKey = $key;
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

		if ($newKey !== $key && isset($attributes[$newKey])) {
			return $attributes[$newKey];
		}

		$manifestKey = $manifestAttrs[$key] ?? null;
		if ($manifestKey === null) {
			[$contextName, $contextType] = self::manifestContext($manifest);
			$tipOutput = isset($manifest['components']) ?
				' If you are using additional components, check if you used the correct block/component prefix in your attribute name.' : '';
			throw new Exception("{$key} key does not exist in the {$contextName} {$contextType} manifest. Please check your implementation.{$tipOutput}");
		}

		// Block-attribute semantics: falsy defaults are treated as undefined when allowed.
		if ($undefinedAllowed && empty($manifestKey['default'])) {
			return null;
		}

		$default = $manifestKey['default'] ?? null;
		if ($default !== null) {
			return $default;
		}

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
	 * @return array<string, mixed>
	 */
	public static function checkAttrResponsive(string $keyName, array $attributes, array $manifest, bool $undefinedAllowed = false): array
	{
		$responsiveAttrs = $manifest['responsiveAttributes'] ?? null;
		if ($responsiveAttrs === null) {
			[$contextName, $contextType] = self::manifestContext($manifest);
			throw new Exception("It looks like you are missing responsiveAttributes key in your {$contextName} {$contextType} manifest.");
		}

		$keyConfig = $responsiveAttrs[$keyName] ?? null;
		if ($keyConfig === null) {
			throw new Exception("It looks like you are missing the {$keyName} key in your manifest responsiveAttributes array.");
		}

		$output = [];
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
	 */
	public static function getAttrKey(string $key, array $attributes, array $manifest): string
	{
		if (isset($manifest['blockName'])) {
			return $key;
		}

		if (\str_contains($key, 'wrapper')) {
			return $key;
		}

		$prefix = $attributes['prefix'] ?? '';
		if ($prefix === '') {
			return $key;
		}

		$componentName = $manifest['componentName'] ?? '';
		if ($componentName === '') {
			return $key;
		}

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
		$output = [];

		$blockName = $attributes['blockName'] ?? '';
		$attributesPrefix = $attributes['prefix'] ?? null;
		$newNameCamel = Helpers::kebabToCamelCase($newName);

		if ($attributesPrefix === null) {
			$prefix = $blockName ? Helpers::kebabToCamelCase($blockName) : '';
		} else {
			$prefix = $attributesPrefix;
		}

		$output['prefix'] = empty($prefix) ? $newNameCamel : $prefix . \ucfirst($newNameCamel);
		$prefixLength = \strlen($output['prefix']);

		foreach ($attributes as $key => $value) {
			if (isset(self::PROPS_PASSTHROUGH_KEYS[$key])) {
				$output[$key] = $value;
			} elseif ($prefixLength > 0 && \str_starts_with($key, (string) $output['prefix'])) {
				$output[$key] = $value;
			}
		}

		if ($manual !== []) {
			$componentPattern = \lcfirst($newNameCamel);

			foreach ($manual as $key => $value) {
				if (isset(self::PROPS_PASSTHROUGH_KEYS[$key])) {
					$output[$key] = $value;
				} else {
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
		$attrs = $manifest['attributes'] ?? null;
		if (!\is_array($attrs) || $attrs === []) {
			return $attributes;
		}

		$defaultAttributes = [];

		$needsKeyTransformation = !isset($manifest['blockName']) &&
			!empty($attributes['prefix']) &&
			!empty($manifest['componentName']);

		$componentNameCamel = null;
		$prefix = null;
		if ($needsKeyTransformation) {
			$componentNameCamel = Helpers::kebabToCamelCase($manifest['componentName']);
			$prefix = $attributes['prefix'];
		}

		foreach ($attrs as $itemKey => $itemValue) {
			if (!isset($itemValue['default'])) {
				continue;
			}

			if ($needsKeyTransformation && !\str_contains((string) $itemKey, 'wrapper')) {
				$newKey = \str_replace($componentNameCamel, $prefix, $itemKey);
			} else {
				$newKey = $itemKey;
			}

			$defaultAttributes[$newKey] = $itemValue['default'];
		}

		// `+` keeps left-side values on key collision, so provided attrs win over defaults without array_merge's reindex cost.
		return $attributes + $defaultAttributes;
	}

	/**
	 * Get html attrs output.
	 *
	 * @param array<string, string> $attrs Array of attributes.
	 * @param bool $escape Escape the attributes.
	 */
	public static function getAttrsOutput(array $attrs, bool $escape = true): string
	{
		$parts = [];

		foreach ($attrs as $key => $value) {
			if ($escape) {
				$value = \esc_attr($value);
				$key = \esc_attr($key);
			}

			// Write key-only form for empty string; keep '0' / non-empty values quoted.
			if ($value !== '') {
				$parts[] = " {$key}='{$value}'";
				continue;
			}

			$parts[] = " {$key}";
		}

		return \implode('', $parts);
	}

	/**
	 * Resolve a human-readable name/type pair for the given manifest, used in error messages.
	 *
	 * @param array<string, mixed> $manifest Block/component manifest data.
	 *
	 * @return array{0: string, 1: string}
	 */
	private static function manifestContext(array $manifest): array
	{
		return [
			$manifest['blockName'] ?? $manifest['componentName'] ?? 'unknown',
			isset($manifest['blockName']) ? 'block' : 'component',
		];
	}
}
