<?php

/**
 * The class file that holds abstract class for REST routes registration
 *
 * @package EightshiftLibs\Rest\Routes
 */

declare(strict_types=1);

namespace EightshiftLibs\Rest\Routes;

use EightshiftLibs\Helpers\Helpers;
use EightshiftLibs\Services\ServiceInterface;
use EightshiftLibs\Rest\RouteInterface;
use WP_REST_Request;
use WP_REST_Server;

/**
 * Abstract base route class
 */
abstract class AbstractRoute implements RouteInterface, ServiceInterface
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

	public const API_RESPONSE_CODE_CONTINUE = 100;
	public const API_RESPONSE_CODE_SWITCHING_PROTOCOLS = 101;
	public const API_RESPONSE_CODE_PROCESSING = 102;
	public const API_RESPONSE_CODE_EARLY_HINTS = 103;

	public const API_RESPONSE_CODE_OK = 200;
	public const API_RESPONSE_CODE_CREATED = 201;
	public const API_RESPONSE_CODE_ACCEPTED = 202;
	public const API_RESPONSE_CODE_NON_AUTHORITATIVE_INFORMATION = 203;
	public const API_RESPONSE_CODE_NO_CONTENT = 204;
	public const API_RESPONSE_CODE_RESET_CONTENT = 205;
	public const API_RESPONSE_CODE_PARTIAL_CONTENT = 206;
	public const API_RESPONSE_CODE_MULTI_STATUS = 207;
	public const API_RESPONSE_CODE_ALREADY_REPORTED = 208;
	public const API_RESPONSE_CODE_IM_USED = 226;

	public const API_RESPONSE_CODE_MULTIPLE_CHOICES = 300;
	public const API_RESPONSE_CODE_MOVED_PERMANENTLY = 301;
	public const API_RESPONSE_CODE_FOUND = 302;
	public const API_RESPONSE_CODE_SEE_OTHER = 303;
	public const API_RESPONSE_CODE_NOT_MODIFIED = 304;
	public const API_RESPONSE_CODE_USE_PROXY = 305;
	public const API_RESPONSE_CODE_TEMPORARY_REDIRECT = 307;
	public const API_RESPONSE_CODE_PERMANENT_REDIRECT = 308;

	public const API_RESPONSE_CODE_BAD_REQUEST = 400;
	public const API_RESPONSE_CODE_UNAUTHORIZED = 401;
	public const API_RESPONSE_CODE_PAYMENT_REQUIRED = 402;
	public const API_RESPONSE_CODE_FORBIDDEN = 403;
	public const API_RESPONSE_CODE_NOT_FOUND = 404;
	public const API_RESPONSE_CODE_METHOD_NOT_ALLOWED = 405;
	public const API_RESPONSE_CODE_NOT_ACCEPTABLE = 406;
	public const API_RESPONSE_CODE_PROXY_AUTHENTICATION_REQUIRED = 407;
	public const API_RESPONSE_CODE_REQUEST_TIMEOUT = 408;
	public const API_RESPONSE_CODE_CONFLICT = 409;
	public const API_RESPONSE_CODE_GONE = 410;
	public const API_RESPONSE_CODE_LENGTH_REQUIRED = 411;
	public const API_RESPONSE_CODE_PRECONDITION_FAILED = 412;
	public const API_RESPONSE_CODE_PAYLOAD_TOO_LARGE = 413;
	public const API_RESPONSE_CODE_URI_TOO_LONG = 414;
	public const API_RESPONSE_CODE_UNSUPPORTED_MEDIA_TYPE = 415;
	public const API_RESPONSE_CODE_RANGE_NOT_SATISFIABLE = 416;
	public const API_RESPONSE_CODE_EXPECTATION_FAILED = 417;
	public const API_RESPONSE_CODE_IM_A_TEAPOT = 418;
	public const API_RESPONSE_CODE_MISDIRECTED_REQUEST = 421;
	public const API_RESPONSE_CODE_UNPROCESSABLE_ENTITY = 422;
	public const API_RESPONSE_CODE_LOCKED = 423;
	public const API_RESPONSE_CODE_FAILED_DEPENDENCY = 424;
	public const API_RESPONSE_CODE_TOO_EARLY = 425;
	public const API_RESPONSE_CODE_UPGRADE_REQUIRED = 426;
	public const API_RESPONSE_CODE_PRECONDITION_REQUIRED = 428;
	public const API_RESPONSE_CODE_TOO_MANY_REQUESTS = 429;
	public const API_RESPONSE_CODE_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
	public const API_RESPONSE_CODE_UNAVAILABLE_FOR_LEGAL_REASONS = 451;
	public const API_RESPONSE_CODE_CLIENT_CLOSED_REQUEST = 499;

	public const API_RESPONSE_CODE_INTERNAL_SERVER_ERROR = 500;
	public const API_RESPONSE_CODE_NOT_IMPLEMENTED = 501;
	public const API_RESPONSE_CODE_BAD_GATEWAY = 502;
	public const API_RESPONSE_CODE_SERVICE_UNAVAILABLE = 503;
	public const API_RESPONSE_CODE_GATEWAY_TIMEOUT = 504;
	public const API_RESPONSE_CODE_HTTP_VERSION_NOT_SUPPORTED = 505;
	public const API_RESPONSE_CODE_VARIANT_ALSO_NEGOTIATES = 506;
	public const API_RESPONSE_CODE_INSUFFICIENT_STORAGE = 507;
	public const API_RESPONSE_CODE_LOOP_DETECTED = 508;
	public const API_RESPONSE_CODE_NOT_EXTENDED = 510;
	public const API_RESPONSE_CODE_NETWORK_AUTHENTICATION_REQUIRED = 511;
	public const API_RESPONSE_CODE_NETWORK_CONNECT_TIMEOUT_ERROR = 599;

	/**
	 * API response code success const.
	 *
	 * @deprecated 10.0.0 Use API_RESPONSE_CODE_OK instead.
	 *
	 * @var int
	 */
	public const API_RESPONSE_CODE_SUCCESS = 200;

	/**
	 * API response code success range const.
	 *
	 * @deprecated 10.0.0 Do not use this constant.
	 *
	 * @var int
	 */
	public const API_RESPONSE_CODE_SUCCESS_RANGE = 299;
	/**
	 * API response code error const.
	 *
	 * @deprecated 10.0.0 Use API_RESPONSE_CODE_BAD_REQUEST instead.
	 *
	 * @var int
	 */
	public const API_RESPONSE_CODE_ERROR = 400;

	/**
	 * API response code error missing const.
	 *
	 * @deprecated 10.0.0 Use API_RESPONSE_CODE_NOT_FOUND instead.
	 *
	 * @var int
	 */
	public const API_RESPONSE_CODE_ERROR_MISSING = 404;

	/**
	 * API response code error forbidden const.
	 *
	 * @deprecated 10.0.0 Use API_RESPONSE_CODE_FORBIDDEN instead.
	 *
	 * @var int
	 */
	public const API_RESPONSE_CODE_ERROR_FORBIDDEN = 403;

	/**
	 * API response code error server const.
	 *
	 * @deprecated 10.0.0 Use API_RESPONSE_CODE_INTERNAL_SERVER_ERROR instead.
	 *
	 * @var int
	 */
	public const API_RESPONSE_CODE_ERROR_SERVER = 500;

	/**
	 * A register method holds register_rest_route function to register api route
	 *
	 * @return void
	 */
	public function register(): void
	{
		\add_action('rest_api_init', [$this, 'routeRegisterCallback']);
	}

	/**
	 * Method that registers rest route that is used inside rest_api_init hook
	 *
	 * @param WP_REST_Server $wpRestServer Server object.
	 *
	 * @return void
	 */
	public function routeRegisterCallback(WP_REST_Server $wpRestServer): void
	{
		\register_rest_route(
			$this->getNamespace() . '/' . $this->getVersion(),
			$this->getRouteName(),
			$this->getCallbackArguments(),
			$this->overrideRoute()
		);
	}

	/**
	 * Method that returns project Route namespace
	 *
	 * @return string Project namespace for REST route.
	 */
	abstract protected function getNamespace(): string;

	/**
	 * Method that returns project route version
	 *
	 * @return string Route version as a string.
	 */
	abstract protected function getVersion(): string;

	/**
	 * Get the base url of the route
	 *
	 * @return string The base URL for route you are adding.
	 */
	abstract protected function getRouteName(): string;

	/**
	 * Get callback arguments array
	 *
	 * @return array<string, mixed> Either an array of options for the endpoint, or an array of arrays for multiple methods.
	 */
	abstract protected function getCallbackArguments(): array;

	/**
	 * Override the existing route
	 *
	 * True overrides, false merges (with newer overriding if duplicate keys exist).
	 *
	 * @return bool If the route already exists, should we override it?
	 */
	protected function overrideRoute(): bool
	{
		return false;
	}


	/**
	 * Extract params from request.
	 * Check if array then output only value that is not empty.
	 *
	 * @param WP_REST_Request $request $request Data got from endpoint url.
	 * @param string $type Request type.
	 *
	 * @return array<string, mixed>
	 */
	protected function getRequestParams(WP_REST_Request $request, string $type = self::CREATABLE): array
	{
		// Check type of request and extract params.
		switch ($type) {
			case self::CREATABLE:
				$params = $request->get_body_params();
				break;
			case self::READABLE:
				$params = $request->get_params();
				break;
			default:
				$params = [];
				break;
		}

		// Check if request maybe has json params usually sent by the Block editor.
		if ($request->get_json_params()) {
			$params = \array_merge(
				$params,
				$request->get_json_params(),
			);
		}

		return $params;
	}

	/**
	 * Convert JS FormData object to usable data in php.
	 *
	 * @param WP_REST_Request $request $request Data got from endpoint url.
	 * @param string $type Request type.
	 *
	 * @return array<string, mixed>
	 */
	protected function prepareSimpleApiParams(WP_REST_Request $request, string $type = self::CREATABLE): array
	{
		return Helpers::sanitizeArray($this->getRequestParams($request, $type), 'sanitize_text_field');
	}

	/**
	 * Check user permission for route action.
	 *
	 * @param string $permission Permission to check.
	 * @param array<string, mixed> $additional Additional data to pass.
	 *
	 * @return array<string, mixed>
	 */
	protected function checkUserPermission(string $permission, array $additional = []): array
	{
		if (\current_user_can($permission)) {
			return [];
		}

		return [
			'status' => self::STATUS_ERROR,
			'code' => self::API_RESPONSE_CODE_ERROR_FORBIDDEN,
			'message' => \__('You don\'t have enough permissions to perform this action!', 'eightshift-libs'),
			'data' => $additional,
		];
	}
}
