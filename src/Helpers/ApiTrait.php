<?php

/**
 * Helpers for API.
 *
 * @package EightshiftLibs\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Helpers;

use EightshiftLibs\Rest\Routes\AbstractRoute;
use WP_REST_Response;

/**
 * Class ApiTrait Helper.
 */
trait ApiTrait
{
	/**
	 * Return API response.
	 *
	 * @param string $msg Message for the user.
	 * @param int $code The code.
	 * @param string $status The status.
	 * @param array<string, mixed> $additional Additional data to attach to response.
	 *
	 * @return WP_REST_Response
	 */
	public static function getApiResponse(
		string $msg,
		int $code = AbstractRoute::API_RESPONSE_CODE_BAD_REQUEST,
		string $status = AbstractRoute::STATUS_ERROR,
		array $additional = []
	): WP_REST_Response {
		$output = [
			'status' => $status,
			'code' => $code,
			'message' => $msg,
		];

		if ($additional) {
			$output['data'] = $additional;
		}

		return new WP_REST_Response($output, $code);
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
