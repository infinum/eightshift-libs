<?php

/**
 * Helpers that are deprecated and will be removed in the next major release.
 *
 * @package EightshiftLibs\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Helpers;

use EightshiftLibs\Exception\InvalidManifest;
use EightshiftLibs\Rest\Routes\AbstractRoute;

/**
 * Class DeprecatedTrait Helper.
 */
trait DeprecatedTrait
{
	/**
	 * Get manifest json by path and name.
	 *
	 * @param string $path Absolute path to.
	 *
	 * @throws InvalidManifest If the manifest is not allowed.
	 *
	 * @deprecated 10.0.0 This method is deprecated and will be removed in the next major release. Every component and block has $manifest variable available by default.
	 *
	 * @return array<string, mixed>
	 */
	public static function getManifestByDir(string $path): array
	{
		$sep = \DIRECTORY_SEPARATOR;
		$root = Helpers::getProjectPaths('src');
		$newPath = \str_replace($root, '', $path);
		$newPath = \array_filter(\explode($sep, $newPath));

		if (!isset($newPath[0]) && $newPath[0] !== 'Blocks') {
			throw InvalidManifest::notAllowedManifestPathException($path);
		}

		if (!isset($newPath[1])) {
			throw InvalidManifest::notAllowedManifestPathException($path);
		}

		switch ($newPath[1]) {
			case 'wrapper':
				return Helpers::getWrapper();
			case 'components':
				return Helpers::getComponent(\end($newPath));
			case 'custom':
				return Helpers::getBlock(\end($newPath));
			default:
				throw InvalidManifest::missingManifestException($path);
		}
	}

	/**
	 * Converts an array of classes into a string which can be echoed.
	 *
	 * @param array<string> $classes Array of classes.
	 *
	 * @deprecated 10.0.0 This method is deprecated and will be removed in the next major release. Replace with clsx.
	 *
	 * @return string
	 */
	public static function classnames(array $classes): string
	{
		return Helpers::clsx($classes);
	}

	/**
	 * Check if provided array is associative or sequential. Will return true if array is sequential.
	 * Optimized to use modern PHP functions when available.
	 *
	 * @param array<string, mixed>|string[] $array Array to check.
	 *
	 * @deprecated Since 10.8.0. Use array_is_list instead.
	 *
	 * @return boolean
	 */
	public static function arrayIsList(array $array): bool
	{
		// Early return for empty array.
		if (empty($array)) {
			return true;
		}

		// Use PHP 8.1+ native function if available (much faster).
		if (\function_exists('array_is_list')) {
			return \array_is_list($array);
		}

		// Fallback optimized implementation.
		return \array_keys($array) === \range(0, \count($array) - 1);
	}

	/**
	 * Check if json is valid with caching for repeated checks.
	 *
	 * @param string $jsonString String to check.
	 *
	 * @deprecated Since 10.8.0. Use json_validate instead.
	 *
	 * @return bool
	 */
	public static function isJson(string $jsonString): bool
	{
		return \json_validate($jsonString);
	}

	/**
	 * Return API success response array.
	 *
	 * @param string $msg Message for the user.
	 * @param array<int|string, mixed> $additional Additional data to attach to response.
	 *
	 * @deprecated 10.0.0 Use getApiResponsePublicOutput instead.
	 *
	 * @return array<string, array<mixed>|int|string>
	 */
	public static function getApiSuccessPublicOutput(string $msg, array $additional = []): array
	{
		$output = [
			'status' => AbstractRoute::STATUS_SUCCESS,
			'code' => AbstractRoute::API_RESPONSE_CODE_OK,
			'message' => $msg,
		];

		if ($additional) {
			$output['data'] = $additional;
		}

		return $output;
	}

	/**
	 * Return API warning response array.
	 *
	 * @param string $msg Msg for the user.
	 * @param array<int|string, mixed> $additional Additional data to attach to response.
	 *
	 * @deprecated 10.0.0 Use getApiResponsePublicOutput instead.
	 *
	 * @return array<string, array<mixed>|int|string>
	 */
	public static function getApiWarningPublicOutput(string $msg, array $additional = []): array
	{
		$output = [
			'status' => AbstractRoute::STATUS_WARNING,
			'code' => AbstractRoute::API_RESPONSE_CODE_OK,
			'message' => $msg,
		];

		if ($additional) {
			$output['data'] = $additional;
		}

		return $output;
	}

	/**
	 * Return API error response array.
	 *
	 * @param string $msg Message for the user.
	 * @param array<string, mixed> $additional Additional data to attach to response.
	 *
	 * @deprecated 10.0.0 Use getApiResponsePublicOutput instead.
	 *
	 * @return array<string, array<mixed>|int|string>
	 */
	public static function getApiErrorPublicOutput(string $msg, array $additional = []): array
	{
		$output = [
			'status' => AbstractRoute::STATUS_ERROR,
			'code' => AbstractRoute::API_RESPONSE_CODE_BAD_REQUEST,
			'message' => $msg,
		];

		if ($additional) {
			$output['data'] = $additional;
		}

		return $output;
	}
}
