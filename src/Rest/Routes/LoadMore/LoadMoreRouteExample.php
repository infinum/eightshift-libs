<?php

/**
 * The class register load more route.
 *
 * @package %g_namespace%\Rest\Routes\LoadMore
 */

declare(strict_types=1);

namespace %g_namespace%\Rest\Routes\LoadMore;

use %g_namespace%\Config\Config;
use %g_use_libs%\Helpers\Components;
use %g_use_libs%\Rest\CallableRouteInterface;
use %g_use_libs%\Rest\Routes\AbstractRoute;
use WP_Error;
use WP_Query;
use WP_REST_Request;

/**
 * Class LoadMoreRouteExample
 */
final class LoadMoreRouteExample extends AbstractRoute implements CallableRouteInterface
{
	/**
	 * Route name constant.
	 *
	 * @var string
	 */
	public const LOAD_MORE_ROUTE = 'load-more-route';

	/**
	 * Method that returns project Route namespace.
	 *
	 * @return string Project namespace EightshiftBoilerplateVendor\for REST route.
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
	 * @return string The base URL for the route you are adding.
	 */
	protected function getRouteName(): string
	{
		return '/' . self::LOAD_MORE_ROUTE;
	}

	/**
	 * Get callback arguments array
	 *
	 * @return array<string, mixed> Either an array of options for the endpoint or an array of arrays for multiple methods.
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
	 * Map load more data response with the component to provide to output.
	 *
	 * @param string $type Type of load more used, usually block name.
	 * @param array<int> $response Response of IDs.
	 *
	 * @return string
	 */
	public function getMappedData(string $type, array $response): string
	{
		switch ($type) {
			case 'featured-content':
				return Components::renderPartial(
					'block',
					$type,
					'cards',
					[
						'items' => $response,
					]
				);
			default:
				return '';
		}
	}

	/**
	 * Method that returns rest response
	 *
	 * @param WP_REST_Request $request Data got from endpoint url.
	 *
	 * @return WP_REST_Response|mixed If response generated an error, WP_Error, if response
	 *                                is already an instance, WP_HTTP_Response, otherwise
	 *                                returns a new WP_REST_Response instance.
	 */
	public function routeCallback(WP_REST_Request $request)
	{
		$params = $request->get_params();

		// Prepare parameters.
		$query = isset($params['query']) ? \json_decode(\sanitize_text_field(\wp_unslash(($params['query']))), true) : [];
		$type = isset($params['type']) ? \sanitize_text_field(\wp_unslash($params['type'])) : '';
		$initialItems = isset($params['initialItems']) ? \json_decode(\sanitize_text_field(\wp_unslash($params['initialItems']))) : '';
		$perPageOverride = isset($params['perPageOverride']) ? \sanitize_text_field(\wp_unslash($params['perPageOverride'])) : '';
		$urlLoadMoreAction = isset($params['urlLoadMoreAction']) ? \sanitize_text_field(\wp_unslash($params['urlLoadMoreAction'])) : 'false';
		$currentPage = isset($params['currentPage']) ? (int) \sanitize_text_field(\wp_unslash($params['currentPage'])) : 0;

		$response = $this->getLoadMoreData(
			$query,
			$type,
			$initialItems,
			$urlLoadMoreAction,
			$currentPage,
			[
				'perPageOverride' => $perPageOverride,
			]
		);

		if (\is_wp_error($response)) {
			return \wp_send_json_error(
				[
					'code' => $response->get_error_code(),
					'msg' => $response->get_error_message(),
				],
				400
			);
		}

		$response['query'] = \wp_json_encode($response['query']);

		return \wp_send_json_success(
			\array_merge(
				[
					'code' => 'success',
					'msg' => \esc_html__('Success.', 'eightshift-boilerplate'),
				],
				$response
			)
		);
	}

	/**
	 * Get load more data.
	 *
	 * @param array<string, mixed> $query WP_Query to request.
	 * @param string $type Type of usage for later mapping.
	 * @param array<string> $initialItems Initial items that are loaded in dom.
	 * @param string $urlLoadMoreAction Action to use, on click on page refresh.
	 * @param integer $currentPage Current page counter.
	 * @param array<string, mixed> $config Config array of overrides.
	 *
	 * @return array<string, mixed>|WP_Error
	 */
	private function getLoadMoreData(array $query, string $type, array $initialItems, string $urlLoadMoreAction, int $currentPage, array $config)
	{
		// Prepare parameters.
		$perPageOverride = $config['perPageOverride'] ?? '';

		// Bailout if the query is missing.
		if (!$query) {
			return new WP_Error(
				'error_missing_query',
				\esc_html__('Your request is missing query param. Query param should be a valid WP_Query arguments.', 'eightshift-boilerplate')
			);
		}

		// Bailout if type is missing.
		if (!$type) {
			return new WP_Error(
				'error_missing_type',
				\esc_html__('Your request is missing type param. This is used to filter which response will the method return.', 'eightshift-boilerplate')
			);
		}

		// Bailout if initialItems is missing.
		if (!$initialItems) {
			return new WP_Error(
				'error_missing_initial_items',
				\esc_html__(
					'Your request is missing initialItems param. This is used to determine the number of items to load and which items to exclude from new requests.',
					'eightshift-boilerplate'
				)
			);
		}

		// Prepare original query.
		$originalQuery = $this->prepareOriginalQuery($query, $initialItems, $perPageOverride);

		// Create new query from original to be able to reference the original query later on.
		$newQuery = $originalQuery;

		// In case we are using api to load data on page refresh via get parameter update per page to new value.
		if ($urlLoadMoreAction === 'true') {
			$newQuery['posts_per_page'] = $newQuery['posts_per_page'] * $currentPage;
		}

		// Prepare response output.
		$response = [];

		// Make a loop for ids.
		$theQuery = new WP_Query($newQuery);

		// Bailout if the query is empty.
		if (!$theQuery->have_posts()) {
			return new WP_Error(
				'error_empty_load_more_query',
				\esc_html__(
					'Your request returns an empty load more query so it doesn\'t show anything. Please check the query sent to ajax.',
					'eightshift-boilerplate'
				)
			);
		}

		// Populate response with IDs.
		$response = $theQuery->posts;

		// Reset global query.
		\wp_reset_postdata();

		// Update offset with new number of items added.
		$newQuery['offset'] = $newQuery['offset'] + $newQuery['posts_per_page'];

		// Return per page to original value if changed.
		$newQuery['posts_per_page'] = $originalQuery['posts_per_page'];

		// Output success.
		return [
			'body' => $this->getMappedData($type, $response),
			'query' => $newQuery,
			'currentPage' => $urlLoadMoreAction === 'true' ? $currentPage : $currentPage + 1 , // If URL, load just remains on the current page number.
			'maxPages' => $this->getMaxCount($originalQuery, $initialItems),
		];
	}

	/**
	 * Detect the max count of a regular query without offsets used for the initial load.
	 *
	 * @param array<string, mixed> $query Array of props for WP_Query.
	 * @param array<string> $initialItems Array of initial items in dom.
	 *
	 * @return int
	 */
	private function getMaxCount(array $query, array $initialItems): int
	{
		$theQuery = new WP_Query($query);

		\wp_reset_postdata();

		$postsNumber = (int) $theQuery->found_posts; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
		$perPage = (int) $query['posts_per_page'];
		$offset = \count($initialItems);

		return (int) \ceil(($postsNumber - $offset) / $perPage);
	}

	/**
	 * Prepare query for general usage.
	 *
	 * @param array<string, mixed> $query Query to append data to.
	 * @param array<string> $initialItems Initial items to query.
	 * @param string $perPageOverride Override per page value.
	 *
	 * @return array<string, mixed>
	 */
	private function prepareOriginalQuery(array $query, array $initialItems, string $perPageOverride): array
	{
		// Force query to get only ids.
		if (!isset($query['fields'])) {
			$query['fields'] = 'ids';
		}

		// Use the initial items count for the number of items if nothing is provided.
		if (!isset($query['posts_per_page'])) {
			$query['posts_per_page'] = \count($initialItems);
		}

		// Override items count if provided.
		if ($perPageOverride) {
			$query['posts_per_page'] = $perPageOverride;
		}

		// Set offset for the initial items loaded in dom.
		if (!isset($query['offset'])) {
			$query['offset'] = \count($initialItems);
		}

		// Remove paged from query is set.
		if (isset($query['paged'])) {
			unset($query['paged']);
		}

		// Prevent accidental issues.
		if (isset($query['nopaging'])) {
			unset($query['nopaging']);
		}

		return $query;
	}
}
