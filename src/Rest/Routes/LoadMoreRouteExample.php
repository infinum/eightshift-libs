<?php

/**
 * The class register route for $className endpoint
 *
 * @package EightshiftLibs\Rest\Routes
 */

declare(strict_types=1);

namespace EightshiftLibs\Rest\Routes;

use EightshiftBoilerplate\Config\Config;
use EightshiftLibs\Helpers\Components;
use EightshiftLibs\Rest\Routes\AbstractRoute;
use EightshiftLibs\Rest\CallableRouteInterface;
use WP_Post_Type;
use WP_REST_Request;

/**
 * Class LoadMoreRoute
 */
class LoadMoreRoute extends AbstractRoute implements CallableRouteInterface
{

	/**
	 * Route Name constant.
	 *
	 * @var string
	 */
	public const ROUTE_NAME = 'load-more';

	/**
	 * Method that returns project Route namespace.
	 *
	 * @return string Project namespace EightshiftLibsVendor\for REST route.
	 */
	protected function getNamespace(): string
	{
		return Config::getProjectRoutesNamespace();
	}

	/**
	 * Method that returns project route version.
	 *
	 * @return string Route version as a string.
	 */
	protected function getVersion(): string
	{
		return Config::getProjectRoutesVersion();
	}

	/**
	 * Get the base url of the route
	 *
	 * @return string The base URL for route you are adding.
	 */
	protected function getRouteName(): string
	{
		return '/' . self::ROUTE_NAME;
	}

	/**
	 * Get callback arguments array
	 *
	 * @return array Either an array of options for the endpoint, or an array of arrays for multiple methods.
	 */
	protected function getCallbackArguments(): array
	{
		return [
			'methods' => static::READABLE,
			'callback' => [$this, 'routeCallback'],
			'permission_callback' => '__return_true'
		];
	}

	/**
	 * Method that returns rest response
	 *
	 * @param \WP_REST_Request $request Data got from endpoint url.
	 *
	 * @return \WP_REST_Response|mixed If response generated an error, WP_Error, if response
	 *                                is already an instance, WP_HTTP_Response, otherwise
	 *                                returns a new WP_REST_Response instance.
	 */
	public function routeCallback(\WP_REST_Request $request)
	{
		$params = $request->get_params();

		$postType = $params['post_type'] ?? '';
		$blockName = $params['blockName'] ?? '';

		// Bailout if no post type is not provided.
		if (!$postType) {
			return \rest_ensure_response([
				'code' => 'error_missing_post_type',
				'data' => [
					'status' => 400,
					'body' => '',
				],
				'headers' => [],
			]);
		}

		$postTypeDetails = get_post_type_object($postType);

		// Bailout if post type details are wrong.
		if (!$postTypeDetails instanceof WP_Post_Type) {
			return \rest_ensure_response([
				'code' => 'error_missing_post_type_details',
				'data' => [
					'status' => 400,
					'body' => '',
				],
				'headers' => [],
			]);
		}

		$routeNamespace = $postTypeDetails->rest_namespace ?? '';
		$routeBase = $postTypeDetails->rest_base ?? '';

		// Bailout if no post type route information is missing.
		if (!$routeNamespace || !$routeBase) {
			return \rest_ensure_response([
				'code' => 'error_missing_post_type_route_details',
				'data' => [
					'status' => 400,
					'body' => '',
				],
				'headers' => [],
			]);
		}

		unset($params['post_type']);
		unset($params['fields']);
		unset($params['blockName']);

		$params['_fields'] = 'id';

		if (isset($params['posts_per_page'])) {
			$params['per_page'] = $params['posts_per_page'];
			unset($params['posts_per_page']);
		}

		$request = new WP_REST_Request( 'GET', "/{$routeNamespace}/{$routeBase}");
		$request->set_query_params($params);

		$data = rest_do_request($request);

		$outputData = $data->get_data();

		if ($data->is_error()) {
			return $outputData;
		}

		$output = '';

		switch ($blockName) {
			case 'featured-content':
				$output = Components::render(
					'featured-content-card.php',
					[
						'ids' => array_map(
							static function ($item) {
								return $item['id'];
							},
							$outputData
						),
						'ssr' => false,
					],
					dirname(__DIR__, 2) . '/Blocks/custom/featured-content/content'
				);

				break;
			
			default:
				$output = '';
				break;
		}

		return \rest_ensure_response([
			'code' => 'success',
			'data' => [
				'status' => 200,
				'body' => $output,
			],
			'headers' => array_merge(
				$data->get_headers(),
				[
					'X-WP-Count' => count($outputData),
				]
			),
		]);
	}
}
