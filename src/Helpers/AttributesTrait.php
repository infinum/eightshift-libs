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

		// Get the correct key for the check in the attributes object.
		$newKey = self::getAttrKey($key, $attributes, $manifest);

		// If key exists in the attributes object, just return that key value.
		if (isset($attributes[$newKey])) {
			return $attributes[$newKey];
		};

		$manifestKey = $manifest['attributes'][$key] ?? null;

		if ($manifestKey === null) {
			if (isset($manifest['blockName']) || \array_key_exists('blockName', $manifest)) {
				throw new Exception("{$key} key does not exist in the {$manifest['blockName']} block manifest. Please check your implementation.");
			} else {
				throw new Exception("{$key} key does not exist in the {$manifest['componentName']} component manifest. Please check your implementation.");
			}
		}

		// If undefinedAllowed is true and attribute is missing default just return null to be able to recognize non set variable.
		if (empty($manifestKey['default']) && $undefinedAllowed) {
			return;
		}

		$defaultType = $manifestKey['type'];

		switch ($defaultType) {
			case 'boolean':
				$defaultValue = $manifestKey['default'] ?? false;
				break;
			case 'array':
			case 'object':
				$defaultValue = $manifestKey['default'] ?? [];
				break;
			default:
				$defaultValue = $manifestKey['default'] ?? '';
				break;
		}

		return $defaultValue;
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
		$output = [];

		if (!isset($manifest['responsiveAttributes'])) {
			if (isset($manifest['blockName']) || \array_key_exists('blockName', $manifest)) {
				throw new Exception("It looks like you are missing responsiveAttributes key in your {$manifest['blockName']} block manifest.");
			} else {
				throw new Exception("It looks like you are missing responsiveAttributes key in your {$manifest['componentName']} component manifest.");
			}
		}

		if (!isset($manifest['responsiveAttributes'][$keyName])) {
			throw new Exception("It looks like you are missing the {$keyName} key in your manifest responsiveAttributes array.");
		}

		foreach ($manifest['responsiveAttributes'][$keyName] as $key => $value) {
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
		// Just skip if attribute is wrapper.
		if (\strpos($key, 'wrapper') !== false) {
			return $key;
		}

		// Skip if using this helper in block.
		if (isset($manifest['blockName'])) {
			return $key;
		}

		// If missing prefix or prefix is empty return key.
		if (!isset($attributes['prefix']) || $attributes['prefix'] === '') {
			return $key;
		}

		// No need to test if this is block or component because on top level block there is no prefix.
		// If there is a prefix, remove the attribute component name prefix and replace it with the new prefix.
		return (string)\str_replace(Components::kebabToCamelCase($manifest['componentName']), $attributes['prefix'], $key);
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

		// Check what attributes we need to includes.
		$includes = [
			'blockName',
			'blockClientId',
			'blockFullName',
			'blockWrapClass',
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

		$blockName = $attributes['blockName'] ?? '';

		// Populate prefix key for recursive checks of attribute names.
		$prefix = (!isset($attributes['prefix'])) ? Components::kebabToCamelCase($blockName) : $attributes['prefix'];

		// Set component prefix.
		if (empty($prefix)) {
			$output['prefix'] = Components::kebabToCamelCase($newName);
		} else {
			$output['prefix'] = $prefix . \ucfirst(Components::kebabToCamelCase($newName));
		}

		// Iterate over the attributes.
		foreach ($attributes as $key => $value) {
			// Include attributes from iteration.
			if (\in_array($key, $includes, true)) {
				$output[$key] = $value;
				continue;
			}

			// If attribute starts with the prefix key leave it in the object if not remove it.
			if (\substr((string)$key, 0, \strlen($output['prefix'])) === $output['prefix']) {
				$output[$key] = $value;
			}
		}

		// Check if you have manual object and prepare the attribute keys and merge them with the original attributes for output.
		if ($manual) {
			// Iterate manual attributes.
			foreach ($manual as $key => $value) {
				// Include attributes from iteration.
				if (\in_array($key, $includes, true)) {
					$output[$key] = $value;
					continue;
				}

				// Remove the current component name from the attribute name.
				$newKey = \str_replace(\lcfirst(Components::kebabToCamelCase($newName)), '', $key);

				// Remove the old key.
				unset($manual[$key]);

				// Add new key to the output with prepared attribute name.
				$manual[$output['prefix'] . \ucfirst($newKey)] = $value;
			}

			// Merge manual and output objects to one.
			$output = \array_merge($output, $manual);
		}

		// Return the original attribute for optimization purposes.
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
	private static function getDefaultRenderAttributes(array $manifest, array $attributes): array
	{
		$defaultAttributes = [];

		if (!\is_iterable($manifest['attributes'])) {
			return [];
		}

		foreach ($manifest['attributes'] as $itemKey => $itemValue) {
			// Get the correct key for the check in the attributes object.
			$newKey = self::getAttrKey($itemKey, $attributes, $manifest);

			if (isset($itemValue['default'])) {
				$defaultAttributes[$newKey] = $itemValue['default'];
			}
		}

		return \array_merge($defaultAttributes, $attributes);
	}
}
