<?php

/**
 * The class register DynamicData used to fetch data via admin ajax.
 *
 * @package EightshiftLibs\DynamicData
 */

declare(strict_types=1);

namespace EightshiftLibs\DynamicData;

use EightshiftLibs\Helpers\Components;

/**
 * DynamicData class.
 */
class DynamicData extends AbstractDynamicData
{
	/**
	 * A register method holds ajax hooks.
	 *
	 * @return void
	 */
	public function register(): void
	{
		\add_action('wp_ajax_dynamic_data', [$this, 'getDynamicDataAjax']);
		\add_action('wp_ajax_nopriv_dynamic_data', [$this, 'getDynamicDataAjax']);
	}

	/**
	 * Map dynamic data reponse with the component to provide to output.
	 *
	 * @param string $type Type of load more used, usually block name.
	 * @param array $response Response of IDs.
	 *
	 * @return string
	 */
	public function getMappedDynamicData(string $type, array $response): string
	{
		switch ($type) {
			case 'featured-content':
				return Components::render(
					'cards.php',
					[
						'ids' => $response,
						'blockSsr' => true,
					],
					dirname(__DIR__, 1) . '/Blocks/custom/featured-content/partials',
					true
				);
				break;
			default:
				return '';
				break;
		}
	}
}
