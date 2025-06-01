<?php

/**
 * Helpers for API.
 *
 * @package EightshiftLibs\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Helpers;

use EightshiftLibs\Rest\Routes\AbstractRoute;

/**
 * Class ApiTrait Helper.
 */
trait ApiTrait
{
	/**
	 * Return API success response array.
	 *
	 * @param string $msg Message for the user.
	 * @param array<int|string, mixed> $additional Additional data to attach to response.
	 *
	 * @return array<string, array<mixed>|int|string>
	 */
	public static function getApiSuccessPublicOutput(string $msg, array $additional = []): array
	{
		$output = [
			'status' => AbstractRoute::STATUS_SUCCESS,
			'code' => AbstractRoute::API_RESPONSE_CODE_SUCCESS,
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
	 * @return array<string, array<mixed>|int|string>
	 */
	public static function getApiWarningPublicOutput(string $msg, array $additional = []): array
	{
		$output = [
			'status' => AbstractRoute::STATUS_WARNING,
			'code' => AbstractRoute::API_RESPONSE_CODE_SUCCESS,
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
	 * @return array<string, array<mixed>|int|string>
	 */
	public static function getApiErrorPublicOutput(string $msg, array $additional = []): array
	{
		$output = [
			'status' => AbstractRoute::STATUS_ERROR,
			'code' => AbstractRoute::API_RESPONSE_CODE_ERROR,
			'message' => $msg,
		];

		if ($additional) {
			$output['data'] = $additional;
		}

		return $output;
	}

	/**
	 * Get API route URL data.
	 *
	 * @param string $namespace The namespace.
	 * @param string $version The version.
	 * @param string $path The path.
	 *
	 * @return array<string>
	 */
	public static function getApiRouteUrlData(string $namespace, string $version, string $path): array
	{
		$prefix = \rtrim(\get_rest_url(\get_current_blog_id()), '/');

		return [
			'prefix' => $prefix,
			'namespace' => $namespace,
			'version' => $version,
			'url' => "{$prefix}/{$namespace}/{$version}/{$path}",
		];
	}
}
