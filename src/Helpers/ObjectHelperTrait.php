<?php

/**
 * The object helper specific functionality inside classes.
 * Used in admin or theme side but only inside a class.
 *
 * @package EightshiftLibs\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Helpers;

use DOMDocument;
use EightshiftLibs\Exception\InvalidManifest;

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
		\libxml_use_internal_errors(true);
		$doc = new DOMDocument('1.0', 'utf-8');
		$doc->loadXML($xml);
		$errors = \libxml_get_errors();
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
		\json_decode($string);
		return (\json_last_error() === \JSON_ERROR_NONE);
	}

	/**
	 * Flatten multidimensional array.
	 *
	 * @param array<mixed> $array Multidimensional array.
	 *
	 * @return array<mixed>
	 */
	public static function flattenArray(array $array): array
	{
		$output = [];

		\array_walk_recursive(
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
	 * @param array<mixed> $array Provided array.
	 * @param string $sanitizationFunction WordPress function used for sanitization purposes.
	 *
	 * @return array<mixed>
	 */
	public static function sanitizeArray(array $array, string $sanitizationFunction): array
	{
		$sanitized = [];

		foreach ($array as $key => $value) {
			if (\is_array($value)) {
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
	 * @param array<mixed> $items Items array to sort. Must have order key.
	 *
	 * @return array<mixed>
	 */
	public static function sortArrayByOrderKey(array $items): array
	{
		\usort(
			$items,
			function ($item1, $item2) {
				return $item1['order'] <=> $item2['order'];
			}
		);

		return $items;
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
		$replace = \preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '-$0', $string) ?? '';
		return \ltrim(\strtolower($replace), '-');
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
		return \lcfirst(\str_replace($separator, '', \ucwords($string, $separator)));
	}

	/**
	 * Check if provided array is associative or sequential. Will return true if array is sequential.
	 *
	 * @param array<string, mixed>|string[] $array Array to check.
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
	 * Helper method to check the validity of JSON string
	 *
	 * @link https://stackoverflow.com/a/15198925/629127
	 *
	 * @param string $string JSON string to validate.
	 *
	 * @throws InvalidManifest Error in the case json file has errors.
	 *
	 * @return array<string, mixed> Parsed JSON string into an array.
	 */
	public static function parseManifest(string $string): array
	{
		$result = \json_decode($string, true);

		switch (\json_last_error()) {
			case \JSON_ERROR_NONE:
				$error = '';
				break;
			case \JSON_ERROR_DEPTH:
				$error = \esc_html__('The maximum stack depth has been exceeded.', 'eightshift-libs');
				break;
			case \JSON_ERROR_STATE_MISMATCH:
				$error = \esc_html__('Invalid or malformed JSON.', 'eightshift-libs');
				break;
			case \JSON_ERROR_CTRL_CHAR:
				$error = \esc_html__('Control character error, possibly incorrectly encoded.', 'eightshift-libs');
				break;
			case \JSON_ERROR_SYNTAX:
				$error = \esc_html__('Syntax error, malformed JSON.', 'eightshift-libs');
				break;
			case \JSON_ERROR_UTF8:
				$error = \esc_html__('Malformed UTF-8 characters, possibly incorrectly encoded.', 'eightshift-libs');
				break;
			case \JSON_ERROR_RECURSION:
				$error = \esc_html__('One or more recursive references in the value to be encoded.', 'eightshift-libs');
				break;
			case \JSON_ERROR_INF_OR_NAN:
				$error = \esc_html__('One or more NAN or INF values in the value to be encoded.', 'eightshift-libs');
				break;
			case \JSON_ERROR_UNSUPPORTED_TYPE:
				$error = \esc_html__('A value of a type that cannot be encoded was given.', 'eightshift-libs');
				break;
			default:
				$error = \esc_html__('Unknown JSON error occurred.', 'eightshift-libs');
				break;
		}

		if ($error !== '') {
			throw InvalidManifest::manifestStructureException($error);
		}

		return $result;
	}
}
