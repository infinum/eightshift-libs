<?php

/**
 * The object helper specific functionality for errors.
 *
 * @package EightshiftLibs\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Helpers;

/**
 * Error logger trait.
 */
trait ErrorLoggerTrait
{

	/**
	 * Ensure correct response for rest using handler function.
	 *
	 * @param integer     $code Response Status code.
	 * @param string      $status Response Status name (success/error).
	 * @param string|null $msg Response Message.
	 * @param array|null  $data Response additional data.
	 *
	 * @return \WP_REST_Response|\WP_Error If response generated an error, WP_Error,
	 *                                     if response is already an instance, WP_REST_Response,
	 *                                     otherwise returns a new WP_REST_Response instance.
	 */
	public function restResponseHandler(int $code, string $status, ?string $msg, ?array $data = null)
	{
		$output = [
			'code' => $code,
			'status' => $status,
			'message' => $msg,
			'data' => $data,
		];

		if ($code >= 200 && $code < 300) {
			ob_start();
			\wp_send_json_success($output, $code);
			$response = ob_get_clean();
		} else {
			ob_start();
			\wp_send_json_error(new \WP_Error($output), $code);
			$response = ob_get_clean();
		}

		return \rest_ensure_response($response);
	}
}
