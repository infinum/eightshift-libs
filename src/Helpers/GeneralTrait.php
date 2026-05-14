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
use JsonException;

/**
 * Class General Helper
 */
trait GeneralTrait
{
	/**
	 * Check if XML is a valid document (used for SVG validation).
	 *
	 * @param string $xml Full xml document.
	 *
	 * @return boolean
	 */
	public static function isValidXml(string $xml): bool
	{
		if (\strlen($xml) < 5 || !\str_contains($xml, '<') || !\str_contains($xml, '>')) {
			return false;
		}

		$originalErrorState = \libxml_use_internal_errors(true);
		\libxml_clear_errors();

		try {
			$doc = new DOMDocument('1.0', 'utf-8');
			return $doc->loadXML($xml) && \libxml_get_errors() === [];
		} finally {
			\libxml_use_internal_errors($originalErrorState);
			\libxml_clear_errors();
		}
	}

	/**
	 * Flatten a multidimensional array into a single-level list.
	 *
	 * Null values are skipped; all other scalars (including 0, false, '') are preserved.
	 *
	 * @param array<int|string, mixed> $arrayToFlatten Multidimensional array to flatten.
	 *
	 * @return list<mixed>
	 */
	public static function flattenArray(array $arrayToFlatten): array
	{
		$output = [];

		\array_walk_recursive(
			$arrayToFlatten,
			function ($value) use (&$output): void {
				if ($value !== null) {
					$output[] = $value;
				}
			}
		);

		return $output;
	}

	/**
	 * Find array value by key in a recursive array.
	 *
	 * @param array<int|string, mixed> $array Array to search.
	 * @param string $needle Key name to find.
	 *
	 * @return array<int, mixed>
	 */
	public static function recursiveArrayFind(array $array, string $needle): array
	{
		$iterator = new RecursiveArrayIterator($array);
		$recursive = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::SELF_FIRST);
		$hits = [];

		foreach ($recursive as $key => $value) {
			if ($key === $needle) {
				$hits[] = $value;
			}
		}

		return $hits;
	}

	/**
	 * Sanitize all values in an array recursively.
	 *
	 * Resolves the sanitization function once and reuses it across recursive calls.
	 *
	 * @link https://developer.wordpress.org/themes/theme-security/data-sanitization-escaping/
	 *
	 * @param array<int|string, mixed> $arrayToSanitize Provided array.
	 * @param string $sanitizationFunction WordPress function used for sanitization purposes.
	 *
	 * @return array<int|string, mixed>
	 */
	public static function sanitizeArray(array $arrayToSanitize, string $sanitizationFunction): array
	{
		if ($arrayToSanitize === []) {
			return [];
		}

		if (!\function_exists($sanitizationFunction)) {
			return $arrayToSanitize;
		}

		$callable = $sanitizationFunction(...);

		$walk = static function (array $items) use (&$walk, $callable): array {
			$result = [];
			foreach ($items as $key => $value) {
				$result[$key] = \is_array($value) ? $walk($value) : $callable($value);
			}
			return $result;
		};

		return $walk($arrayToSanitize);
	}

	/**
	 * Sort array by `order` key (used for term ordering).
	 *
	 * @param list<array<string, mixed>> $items Items array to sort. Must have order key.
	 *
	 * @return list<array<string, mixed>>
	 */
	public static function sortArrayByOrderKey(array $items): array
	{
		if (\count($items) < 2) {
			return $items;
		}

		\usort(
			$items,
			fn($a, $b) => ($a['order'] ?? 0) <=> ($b['order'] ?? 0)
		);

		return $items;
	}

	/**
	 * Convert string from camel case to kebab case.
	 *
	 * Handles acronyms (`APIKey` → `api-key`) and existing separators (`foo_bar` → `foo-bar`).
	 *
	 * @param string $input String to convert.
	 *
	 * @return string
	 */
	public static function camelToKebabCase(string $input): string
	{
		if ($input === '') {
			return '';
		}

		$normalized = \str_replace(['_', ' '], '-', $input);
		$output = (string) \preg_replace(
			['/([a-z\d])([A-Z])/', '/([A-Z]+)([A-Z][a-z])/'],
			'$1-$2',
			$normalized
		);

		return \mb_strtolower(\trim($output, '-'));
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
	 * Parse and validate a JSON manifest string.
	 *
	 * @param string $manifest JSON string to validate.
	 *
	 * @throws InvalidManifest When the manifest is empty or contains invalid JSON.
	 *
	 * @return array<string, mixed> Parsed JSON string into an array.
	 */
	public static function parseManifest(string $manifest): array
	{
		if ($manifest === '') {
			throw InvalidManifest::manifestStructureException(\esc_html__('Empty manifest provided.', 'eightshift-libs'));
		}

		try {
			$result = \json_decode($manifest, true, 512, \JSON_THROW_ON_ERROR);
		} catch (JsonException $e) {
			throw InvalidManifest::manifestStructureException(\esc_html($e->getMessage()));
		}

		return \is_array($result) ? $result : [];
	}

	/**
	 * Get the current request URL (including query string).
	 *
	 * Result is cached for the lifetime of the request since the URL cannot change mid-request.
	 *
	 * @return string
	 */
	public static function getCurrentUrl(): string
	{
		static $cached = null;

		if ($cached !== null) {
			return $cached;
		}

		$https = isset($_SERVER['HTTPS']) ? \sanitize_text_field(\wp_unslash($_SERVER['HTTPS'])) : '';
		$host = isset($_SERVER['HTTP_HOST']) ? \sanitize_text_field(\wp_unslash($_SERVER['HTTP_HOST'])) : '';
		$request = isset($_SERVER['REQUEST_URI']) ? \sanitize_text_field(\wp_unslash($_SERVER['REQUEST_URI'])) : '';

		$protocol = ($https !== '' && $https !== 'off') ? 'https' : 'http';
		$cached = "{$protocol}://{$host}{$request}";

		return $cached;
	}

	/**
	 * Strip query string and fragment from a URL.
	 *
	 * @param string $url URL to clean.
	 *
	 * @return string
	 */
	public static function cleanUrlParams(string $url): string
	{
		if ($url === '') {
			return '';
		}

		$cutoff = \strcspn($url, '?#');

		return $cutoff === \strlen($url) ? $url : \substr($url, 0, $cutoff);
	}
}
