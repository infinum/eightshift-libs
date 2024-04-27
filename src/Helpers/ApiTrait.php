<?php

/**
 * Helpers for API.
 *
 * @package EightshiftLibs\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Helpers;

/**
 * Class ApiTrait Helper.
 */
trait ApiTrait
{
	/**
	 * Status error const.
	 *
	 * @var string
	 */
	public const STATUS_ERROR = 'error';

	/**
	 * Status success const.
	 *
	 * @var string
	 */
	public const STATUS_SUCCESS = 'success';

	/**
	 * Status warning const.
	 *
	 * @var string
	 */
	public const STATUS_WARNING = 'warning';

	/**
	 * API response code success const.
	 *
	 * @var int
	 */
	public const API_RESPONSE_CODE_SUCCESS = 200;

	/**
	 * API response code success range const.
	 *
	 * @var int
	 */
	public const API_RESPONSE_CODE_SUCCESS_RANGE = 299;

	/**
	 * API response code error const.
	 *
	 * @var int
	 */
	public const API_RESPONSE_CODE_ERROR = 400;

	/**
	 * API response code error missing const.
	 *
	 * @var int
	 */
	public const API_RESPONSE_CODE_ERROR_MISSING = 404;

	/**
	 * API response code error server const.
	 *
	 * @var int
	 */
	public const API_RESPONSE_CODE_ERROR_SERVER = 500;

	/**
	 * API response code error forbidden const.
	 *
	 * @var int
	 */
	public const API_RESPONSE_CODE_ERROR_FORBIDDEN = 403;

	/**
	 * Return API success response array.
	 *
	 * @param string $msg Msg for the user.
	 * @param array<int|string, mixed> $additional Additonal data to attach to response.
	 *
	 * @return array<string, array<mixed>|int|string>
	 */
	public static function getApiSuccessPublicOutput(string $msg, array $additional = []): array
	{
		$output = [
			'status' => self::STATUS_SUCCESS,
			'code' => self::API_RESPONSE_CODE_SUCCESS,
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
	 * @param array<int|string, mixed> $additional Additonal data to attach to response.
	 *
	 * @return array<string, array<mixed>|int|string>
	 */
	public static function getApiWarningPublicOutput(string $msg, array $additional = []): array
	{
		$output = [
			'status' => self::STATUS_WARNING,
			'code' => self::API_RESPONSE_CODE_SUCCESS,
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
	 * @param string $msg Msg for the user.
	 * @param array<string, mixed> $additional Additonal data to attach to response.
	 *
	 * @return array<string, array<mixed>|int|string>
	 */
	public static function getApiErrorPublicOutput(string $msg, array $additional = []): array
	{
		$output = [
			'status' => self::STATUS_ERROR,
			'code' => self::API_RESPONSE_CODE_ERROR,
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
		$prefix = rtrim(\get_rest_url(\get_current_blog_id()), '/');

		return [
			'prefix' => $prefix,
			'namespace' => $namespace,
			'version' => $version,
			'url' => "{$prefix}/{$namespace}/{$version}/{$path}",
		];
	}
}
