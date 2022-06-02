<?php

/**
 * The abstract class register DynamicData used to fetch data via admin ajax.
 *
 * @package EightshiftLibs\DynamicData
 */

declare(strict_types=1);

namespace EightshiftLibs\DynamicData;

use EightshiftLibs\Services\ServiceInterface;
use WP_Query;

/**
 * AbstractDynamicData class.
 */
abstract class AbstractDynamicData implements ServiceInterface
{
	/**
	 * Map dynamic data reponse with the component to provide to output.
	 *
	 * @param string $type Type of load more used, usually block name.
	 * @param array<int> $response Response of IDs.
	 *
	 * @return string
	 */
	abstract public function getMappedDynamicData(string $type, array $response): string;

	/**
	 * Get Dynamic data ajax response callback.
	 *
	 * @return string
	 */
	public function getDynamicDataAjax(): string
	{
		// Prepare parameters.
		$query = isset($_POST['query']) ? \json_decode(\sanitize_text_field(\wp_unslash(($_POST['query']))), true) : []; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$type = isset($_POST['type']) ? \sanitize_text_field(\wp_unslash($_POST['type'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$initialItems = isset($_POST['initialItems']) ? \json_decode(\sanitize_text_field(\wp_unslash($_POST['initialItems']))) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$perPageOverride = isset($_POST['perPageOverride']) ? \sanitize_text_field(\wp_unslash($_POST['perPageOverride'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$urlLoadMoreAction = isset($_POST['urlLoadMoreAction']) ? \sanitize_text_field(\wp_unslash($_POST['urlLoadMoreAction'])) : 'false'; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$currentPage = isset($_POST['currentPage']) ? (int) \sanitize_text_field(\wp_unslash($_POST['currentPage'])) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing

		// Bailout if query is missing.
		if (!$query) {
			return \wp_send_json_error(
				[
					'code' => 'error_missing_query',
					'msg' => \esc_html__('Your request is missing query param. Query param should be a valid WP_Query arguments.', 'eightshift-boilerplate'),
				],
				400
			);
		}

		// Bailout if type is missing.
		if (!$type) {
			return \wp_send_json_error(
				[
					'code' => 'error_missing_type',
					'msg' => \esc_html__('Your request is missing type param. This is used to filter whate response will the method return.', 'eightshift-boilerplate'),
				],
				400
			);
		}

		// Bailout if initialItems is missing.
		if (!$initialItems) {
			return \wp_send_json_error(
				[
					'code' => 'error_missing_initial_items',
					'msg' => \esc_html__(
						'Your request is missing initialItems param. This is used to determin number of items to load and what items to exclude from new requests.',
						'eightshift-boilerplate'
					),
				],
				400
			);
		}

		// Prepare original query.
		$originalQuery = $this->prepareOriginalQuery($query, $initialItems, $perPageOverride);

		// Create new query to modify of url load.
		$newQuery = $originalQuery;

		// In case on url load update per page to new value.
		if ($urlLoadMoreAction === 'true') {
			$newQuery['posts_per_page'] = $newQuery['posts_per_page'] * $currentPage;
		}

		// Prepare response output.
		$response = [];

		// Make a loop for ids.
		$theQuery = new WP_Query($newQuery);

		// Bailout if query is empty.
		if (!$theQuery->have_posts()) {
			return \wp_send_json_error(
				[
					'code' => 'error_empty_load_more_query',
					'msg' => \esc_html__(
						'Your request returns empty load more query so it can\'t show anything. Please check the query sent to the ajax.',
						'eightshift-boilerplate'
					),
				],
				400
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
		return \wp_send_json_success(
			[
				'code' => 'success',
				'msg' => \esc_html__('Success.', 'eightshift-boilerplate'),
				'body' => $this->getMappedDynamicData($type, $response),
				'query' => \wp_json_encode($newQuery),
				'currentPage' => $urlLoadMoreAction === 'true' ? $currentPage : $currentPage + 1 , // If url load just remain on the current page number.
				'maxPages' => $this->getMaxCount($originalQuery, $initialItems),
			],
			200
		);
	}

	/**
	 * Detect max count of regular query without offsets used for initial load.
	 *
	 * @param array<string, mixed> $query Array of props for WP_Query.
	 * @param array<int> $initialItems Array of initial items in dom.
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
	 * @param array<int> $initialItems Initial items to query.
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

		// Use initial items count for number of items if nothing is provided.
		if (!isset($query['posts_per_page'])) {
			$query['posts_per_page'] = \count($initialItems);
		}

		// Ovderride items count if provided.
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

		// Prevend accidental issues.
		if (isset($query['nopaging'])) {
			unset($query['nopaging']);
		}

		return $query;
	}
}
