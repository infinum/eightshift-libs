<?php

/**
 * The general helper specific functionality.
 *
 * @package EightshiftLibs\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Helpers;

use DOMDocument;
use EightshiftLibs\Exception\InvalidManifest;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

/**
 * Class General Helper
 */
trait GeneralTrait
{
	/**
	 * Check if XML is valid file used for svg.
	 * Optimized with early validation and error handling.
	 *
	 * @param string $xml Full xml document.
	 *
	 * @return boolean
	 */
	public static function isValidXml(string $xml): bool
	{
		// Early return for empty or very short strings.
		if (\strlen($xml) < 5) {
			return false;
		}

		// Quick check for basic XML structure.
		if (!\str_contains($xml, '<') || !\str_contains($xml, '>')) {
			return false;
		}

		// Suppress errors during validation.
		$originalErrorState = \libxml_use_internal_errors(true);

		try {
			$doc = new DOMDocument('1.0', 'utf-8');
			$doc->strictErrorChecking = false;
			$doc->recover = true;

			$result = $doc->loadXML($xml);
			$errors = \libxml_get_errors();

			return $result && empty($errors);
		} finally {
			// Restore original settings.
			\libxml_use_internal_errors($originalErrorState);
			\libxml_clear_errors();
		}
	}

	/**
	 * Flatten multidimensional array with optimized performance.
	 *
	 * @param array<mixed> $arrayToFlatten Multidimensional array to flatten.
	 *
	 * @return array<mixed>
	 */
	public static function flattenArray(array $arrayToFlatten): array
	{
		$output = [];

		\array_walk_recursive(
			$arrayToFlatten,
			function ($a) use (&$output) {
				if (!empty($a)) {
					$output[] = $a;
				}
			}
		);

		return $output;
	}

	/**
	 * Find array value by key in recursive array with optimized search.
	 *
	 * @param array<mixed> $array Array to find.
	 * @param string $needle Key name to find.
	 *
	 * @return array<int, string>
	 */
	public static function recursiveArrayFind(array $array, string $needle): array
	{
		$iterator  = new RecursiveArrayIterator($array);
		$recursive = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::SELF_FIRST);
		$aHitList = [];

		foreach ($recursive as $key => $value) {
			if ($key === $needle) {
				\array_push($aHitList, $value);
			}
		}

		return $aHitList;
	}

	/**
	 * Sanitize all values in an array with optimized recursion.
	 *
	 * @link https://developer.wordpress.org/themes/theme-security/data-sanitization-escaping/
	 *
	 * @param array<mixed> $arrayToSanitize Provided array.
	 * @param string $sanitizationFunction WordPress function used for sanitization purposes.
	 *
	 * @return array<mixed>
	 */
	public static function sanitizeArray(array $arrayToSanitize, string $sanitizationFunction): array
	{
		// Early return for empty array.
		if (empty($arrayToSanitize)) {
			return [];
		}

		// Validate function exists.
		if (!\function_exists($sanitizationFunction)) {
			return $arrayToSanitize;
		}

		$sanitized = [];

		foreach ($arrayToSanitize as $key => $value) {
			if (\is_array($value)) {
				$sanitized[$key] = self::sanitizeArray($value, $sanitizationFunction);
			} else {
				$sanitized[$key] = $sanitizationFunction($value);
			}
		}

		return $sanitized;
	}

	/**
	 * Sort array by order key. Used to sort terms.
	 * Already optimized but added safety checks.
	 *
	 * @param array<mixed> $items Items array to sort. Must have order key.
	 *
	 * @return array<mixed>
	 */
	public static function sortArrayByOrderKey(array $items): array
	{
		// Early return for arrays with less than 2 items.
		if (\count($items) < 2) {
			return $items;
		}

		\usort(
			$items,
			function ($item1, $item2) {
				$order1 = $item1['order'] ?? 0;
				$order2 = $item2['order'] ?? 0;
				return $order1 <=> $order2;
			}
		);

		return $items;
	}

	/**
	 * Convert string from camel to kebab case.
	 *
	 * @param string $input String to convert.
	 *
	 * @return string
	 */
	public static function camelToKebabCase(string $input): string
	{
		// Early return for empty string.
		if ($input === '') {
			return '';
		}

		// Optimized conversion using modern PHP functions.
		$output = \ltrim(\mb_strtolower((string)\preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '-$0', $input)), '-');
		return \str_replace(['_', ' ', '--'], ['-', '-', '-'], $output);
	}

	/**
	 * Convert camel to snake case.
	 *
	 * @param string $input String to convert.
	 *
	 * @return string
	 */
	public static function camelToSnakeCase(string $input): string
	{
		// Early return for empty string.
		if ($input === '') {
			return '';
		}

		return \strtolower((string) \preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
	}

	/**
	 * Convert string from kebab to camel case.
	 *
	 * @param string $input String to convert.
	 * @param string $separator Separator to use for conversion.
	 *
	 * @return string
	 */
	public static function kebabToCamelCase(string $input, string $separator = '-'): string
	{
		return \lcfirst(\str_replace($separator, '', \ucwords($input, $separator)));
	}

	/**
	 * Convert string from kebab to snake case.
	 *
	 * @param string $input String to convert.
	 *
	 * @return string
	 */
	public static function kebabToSnakeCase(string $input): string
	{
		// Early return for empty string.
		if ($input === '') {
			return '';
		}

		return \str_replace('-', '_', $input);
	}

	/**
	 * Helper method to check the validity of JSON string with optimized error handling.
	 *
	 * @link https://stackoverflow.com/a/15198925/629127
	 *
	 * @param string $manifest JSON string to validate.
	 *
	 * @throws InvalidManifest Error in the case json file has errors.
	 *
	 * @return array<string, mixed> Parsed JSON string into an array.
	 */
	public static function parseManifest(string $manifest): array
	{
		// Early return for empty manifest.
		if ($manifest === '') {
			throw InvalidManifest::manifestStructureException(\esc_html__('Empty manifest provided.', 'eightshift-libs'));
		}

		$result = \json_decode($manifest, true);
		$jsonError = \json_last_error();

		// Fast path for no errors.
		if ($jsonError === \JSON_ERROR_NONE) {
			return $result;
		}

		// Optimized error handling using lookup table.
		$errorMessages = [
			\JSON_ERROR_DEPTH => \esc_html__('The maximum stack depth has been exceeded.', 'eightshift-libs'),
			\JSON_ERROR_STATE_MISMATCH => \esc_html__('Invalid or malformed JSON.', 'eightshift-libs'),
			\JSON_ERROR_CTRL_CHAR => \esc_html__('Control character error, possibly incorrectly encoded.', 'eightshift-libs'),
			\JSON_ERROR_SYNTAX => \esc_html__('Syntax error, malformed JSON.', 'eightshift-libs'),
			\JSON_ERROR_UTF8 => \esc_html__('Malformed UTF-8 characters, possibly incorrectly encoded.', 'eightshift-libs'),
			\JSON_ERROR_RECURSION => \esc_html__('One or more recursive references in the value to be encoded.', 'eightshift-libs'),
			\JSON_ERROR_INF_OR_NAN => \esc_html__('One or more NAN or INF values in the value to be encoded.', 'eightshift-libs'),
			\JSON_ERROR_UNSUPPORTED_TYPE => \esc_html__('A value of a type that cannot be encoded was given.', 'eightshift-libs'),
		];

		$error = $errorMessages[$jsonError] ?? \esc_html__('Unknown JSON error occurred.', 'eightshift-libs');

		throw InvalidManifest::manifestStructureException($error);
	}

	/**
	 * Get current URL with params using optimized string building.
	 *
	 * @return string
	 */
	public static function getCurrentUrl(): string
	{
		// Cache server variables to avoid repeated sanitization.
		static $cachedUrl = null;
		static $lastRequestTime = null;
		$currentTime = isset($_SERVER['REQUEST_TIME']) ? \sanitize_text_field(\wp_unslash($_SERVER['REQUEST_TIME'])) : \time();

		// Return cached URL if it's from the same request.
		if ($cachedUrl !== null && $lastRequestTime === $currentTime) {
			return $cachedUrl;
		}

		$isHttps = isset($_SERVER['HTTPS']) && \sanitize_text_field(\wp_unslash($_SERVER['HTTPS']));
		$host = isset($_SERVER['HTTP_HOST']) ? \sanitize_text_field(\wp_unslash($_SERVER['HTTP_HOST'])) : '';
		$request = isset($_SERVER['REQUEST_URI']) ? \sanitize_text_field(\wp_unslash($_SERVER['REQUEST_URI'])) : '';

		// Optimized URL building.
		$protocol = $isHttps ? 'https' : 'http';
		$cachedUrl = "{$protocol}://{$host}{$request}";
		$lastRequestTime = $currentTime;

		return $cachedUrl;
	}

	/**
	 * Clean url from query params using optimized string operations.
	 *
	 * @param string $url URL to clean.
	 *
	 * @return string
	 */
	public static function cleanUrlParams(string $url): string
	{
		// Early return for empty URL.
		if ($url === '') {
			return '';
		}

		// Fast path using strpos instead of preg_replace for simple cases.
		$queryPos = \strpos($url, '?');
		if ($queryPos === false) {
			return $url;
		}

		return \substr($url, 0, $queryPos);
	}
}
