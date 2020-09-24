<?php

/**
 * The Blog_Taxonomy specific functionality.
 *
 * @package EightshiftLibs\CustomTaxonomy
 */

declare(strict_types=1);

namespace EightshiftLibs\CustomTaxonomy;

use EightshiftLibs\CustomTaxonomy\AbstractTaxonomy;

/**
 * Class TaxonomyExample
 */
class TaxonomyExample extends AbstractTaxonomy
{

	/**
	 * Taxonomy slug constant.
	 *
	 * @var string
	 */
	public const TAXONOMY_SLUG = 'example-slug';

	/**
	 * Rest API Endpoint slug constant.
	 *
	 * @var string
	 */
	public const REST_API_ENDPOINT_SLUG = 'example-endpoint-slug';

	/**
	 * Get the slug of the custom taxonomy
	 *
	 * @return string Custom taxonomy slug.
	 */
	protected function getTaxonomySlug(): string
	{
		return static::TAXONOMY_SLUG;
	}

	/**
	 * Get the post type slug(s) that use the taxonomy.
	 *
	 * @return string|array Custom post type slug or an array of slugs.
	 */
	protected function getPostTypeSlug()
	{
		return 'post';
	}

	/**
	 * Get the arguments that configure the custom taxonomy.
	 *
	 * @return array Array of arguments.
	 */
	protected function getTaxonomyArguments(): array
	{
		return [
			'hierarchical' => true,
			'label' => \esc_html__('Example Name', 'eightshift-libs'),
			'show_ui' => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => false,
			'public' => true,
			'show_in_rest' => true,
			'query_var' => true,
			'rest_base' => static::REST_API_ENDPOINT_SLUG,
			'rewrite' => array(
				'hierarchical' => true,
				'with_front' => false,
			),
		];
	}
}
