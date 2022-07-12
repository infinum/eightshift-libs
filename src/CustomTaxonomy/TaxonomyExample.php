<?php

/**
 * The Blog_Taxonomy specific functionality.
 *
 * @package EightshiftBoilerplate\CustomTaxonomy
 */

declare(strict_types=1);

namespace EightshiftBoilerplate\CustomTaxonomy;

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
	public const TAXONOMY_SLUG = '%slug%';

	/**
	 * Taxonomy post type slug constant.
	 *
	 * @var string
	 */
	public const TAXONOMY_POST_TYPE_SLUG = '%post_type_slug%';

	/**
	 * Rest API Endpoint slug constant.
	 *
	 * @var string
	 */
	public const REST_API_ENDPOINT_SLUG = '%rest_endpoint_slug%';

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
	 * @return string|array<string> Custom post type slug or an array of slugs.
	 */
	protected function getPostTypeSlug()
	{
		return self::TAXONOMY_POST_TYPE_SLUG;
	}

	/**
	 * Get the arguments that configure the custom taxonomy.
	 *
	 * @return array<mixed> Array of arguments.
	 */
	protected function getTaxonomyArguments(): array
	{
		$nouns = [
			\esc_html_x(
				'%label%',
				'taxonomy upper case singular name',
				'eightshift-libs'
			),
			\esc_html_x(
				'%label_lowercaps%',
				'taxonomy lower case singular name',
				'eightshift-libs'
			),
			\esc_html_x(
				'%plural_label%',
				'taxonomy upper case plural name',
				'eightshift-libs'
			),
			\esc_html_x(
				'%plural_label_lowecaps%',
				'taxonomy lower case plural name',
				'eightshift-libs'
			),
		];

		$labels = $this->getGeneratedLabels($nouns);

		return [
			'hierarchical' => true,
			'label' => $nouns[0],
			'labels' => $labels,
			'show_ui' => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => false,
			'public' => true,
			'show_in_rest' => true,
			'query_var' => true,
			'rest_base' => static::REST_API_ENDPOINT_SLUG,
			'rewrite' => [
				'hierarchical' => true,
				'with_front' => false,
			],
		];
	}
}
