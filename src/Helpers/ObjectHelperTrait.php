<?php

/**
 * The object helper specific functionality inside classes.
 * Used in admin or theme side but only inside a class.
 *
 * @package EightshiftLibs\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Helpers;

/**
 * Class Object Helper
 */
trait ObjectHelperTrait
{
	/**
	 * Check if XML is valid file used for svg.
	 *
	 * @param string $xml Full xml document.
	 *
	 * @return boolean
	 */
	public function isValidXml(string $xml)
	{
		libxml_use_internal_errors(true);
		$doc = new \DOMDocument('1.0', 'utf-8');
		$doc->loadXML($xml);
		$errors = libxml_get_errors();
		return empty($errors);
	}

	/**
	 * Check if json is valid
	 *
	 * @param string $string String to check.
	 *
	 * @return bool
	 */
	public static function isJson(string $string): bool
	{
		json_decode($string);
		return (json_last_error() === JSON_ERROR_NONE);
	}

	/**
	 * Flatten multidimensional array.
	 *
	 * @param array $array Multidimensional array.
	 *
	 * @return array
	 */
	public static function flattenArray(array $array): array
	{
		$output = [];

		array_walk_recursive(
			$array,
			function ($a) use (&$output) {
				if (!empty($a)) {
					$output[] = $a;
				}
			}
		);

		return $output;
	}

	/**
	 * Sanitize all values in an array.
	 *
	 * @link https://developer.wordpress.org/themes/theme-security/data-sanitization-escaping/
	 *
	 * @param array  $array Provided array.
	 * @param string $sanitizationFunction WordPress function used for sanitization purposes.
	 *
	 * @return array
	 */
	public static function sanitizeArray(array $array, string $sanitizationFunction): array
	{
		$sanitized = [];

		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$sanitizedValue = self::sanitizeArray($value, $sanitizationFunction);
				$sanitized[$key] = $sanitizedValue;

				continue;
			}

			$sanitized[$key] = $sanitizationFunction($value);
		}

		return $sanitized;
	}

	/**
	 * Sort array by order key. Used to sort terms.
	 *
	 * @param array $items Items array to sort. Must have order key.
	 * @return array
	 */
	public static function sortArrayByOrderKey(array $items): array
	{
		usort(
			$items,
			function ($item1, $item2) {
				return $item1['order'] <=> $item2['order'];
			}
		);

		return $items;
	}
}
