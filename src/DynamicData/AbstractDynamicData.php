<?php

/**
 * The abstract class register DynamicData used to fetch data via admin ajax.
 *
 * @package EightshiftBoilerplate\DynamicData\DynamicData
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
	 * @param array $response Response of IDs.
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
		// Set max count to init value.
		$maxCount = 0;

		// Get count from ajax.
		$count = isset($_POST['count']) ? (int) \sanitize_text_field(\wp_unslash($_POST['count'])) : 0;

		// Bailout if count is missing.
		if (!$count) {
			return \wp_send_json_error(
				[
					'code' => 'error_missing_count',
					'msg' => __('Your request is missing count param. Usually this is a page number.', 'eightshift-boilerplate'),
					'body' => '',
					'maxCount' => $maxCount,
					'currentCount' => $count,
				],
				400
			);
		}

		// Get query from ajax.
		$query = isset($_POST['query']) ? \json_decode(\sanitize_text_field(\wp_unslash($_POST['query'])), true) : '';

		// Bailout if query is missing.
		if (!$query) {
			return \wp_send_json_error(
				[
					'code' => 'error_missing_query',
					'msg' => __('Your request is missing query param. Query param should be a valid WP_Query arguments.', 'eightshift-boilerplate'),
					'body' => '',
					'maxCount' => $maxCount,
					'currentCount' => $count,
				],
				400
			);
		}

		// Get type from ajax.
		$type = isset($_POST['type']) ? \sanitize_text_field(\wp_unslash($_POST['type'])) : '';

		// Bailout if type is missing.
		if (!$type) {
			return \wp_send_json_error(
				[
					'code' => 'error_missing_type',
					'msg' => __('Your request is missing type param. This is used to filter whate response will the method return.', 'eightshift-boilerplate'),
					'body' => '',
					'maxCount' => $maxCount,
					'currentCount' => $count,
				],
				400
			);
		}

		// Force query to get only ids.
		if (!isset($query['fields'])) {
			$query['fields'] = 'ids';
		}

		// Force paged number get from ajax.
		$query['paged'] = $count;

		// Make a loop.
		$theQuery = new WP_Query($query);

		// Prepare response output.
		$response = [];

		// Do a loop.
		if ($theQuery->have_posts()) {
			while ( $theQuery->have_posts() ) {
				$theQuery->the_post();

				// Populate response with IDs.
				$response[] = get_the_ID();
			}
		}

		// Reset global query.
		wp_reset_postdata();

		// Output success.
		return \wp_send_json_success(
			[
				'code' => 'success',
				'msg' => __('Success.', 'eightshift-boilerplate'),
				'body' => $this->getMappedDynamicData($type, $response),
				'maxCount' => (int) $theQuery->max_num_pages,
				'currentCount' => $count,
			],
			200
		);
	}
}
