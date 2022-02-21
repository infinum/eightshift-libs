<?php

/**
 * File that holds class for PostTypeExample custom post type registration.
 *
 * @package EightshiftBoilerplate\CustomPostType
 */

declare(strict_types=1);

namespace EightshiftBoilerplate\CustomPostType;

use EightshiftLibs\CustomPostType\AbstractPostType;

/**
 * Class PostTypeExample.
 */
class PostTypeExample extends AbstractPostType
{
	/**
	 * Post type slug constant.
	 *
	 * @var string
	 */
	public const POST_TYPE_SLUG = 'example-slug';

	/**
	 * URL slug for the custom post type.
	 *
	 * @var string
	 */
	public const POST_TYPE_URL_SLUG = 'example-url-slug';

	/**
	 * Rest API Endpoint slug constant.
	 *
	 * @var string
	 */
	public const REST_API_ENDPOINT_SLUG = 'example-endpoint-slug';

	/**
	 * Capability type for projects post type.
	 *
	 * @var string
	 */
	public const POST_CAPABILITY_TYPE = 'post';

	/**
	 * Location of menu in sidebar.
	 *
	 * @var int
	 */
	public const MENU_POSITION = 20;

	/**
	 * Set menu icon.
	 *
	 * @var string
	 */
	public const MENU_ICON = 'dashicons-analytics';

	/**
	 * Get the slug to use for the Projects custom post type.
	 *
	 * @return string Custom post type slug.
	 */
	protected function getPostTypeSlug(): string
	{
		return self::POST_TYPE_SLUG;
	}

	/**
	 * Get the arguments that configure the Projects custom post type.
	 *
	 * @return array<mixed> Array of arguments.
	 */
	protected function getPostTypeArguments(): array
	{
		$nouns = [
			self::SINGULAR_NAME_UC => \esc_html_x(
				'Singular Name',
				'post type upper case singular name',
				'eightshift-libs'
			),
			self::SINGULAR_NAME_LC => \esc_html_x(
				'singular name',
				'post type lower case singular name',
				'eightshift-libs'
			),
			self::PLURAL_NAME_UC => \esc_html_x(
				'Plural Name',
				'post type upper case plural name',
				'eightshift-libs'
			),
			self::PLURAL_NAME_LC => \esc_html_x(
				'plural name',
				'post type lower case plural name',
				'eightshift-libs'
			),
		];

		$labels = $this->getGeneratedLabels($nouns);

		return [
			'label' => $nouns[self::SINGULAR_NAME_UC],
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'query_var' => true,
			'capability_type' => self::POST_CAPABILITY_TYPE,
			'has_archive' => true,
			'rewrite' => ['slug' => static::POST_TYPE_URL_SLUG],
			'hierarchical' => false,
			'menu_icon' => static::MENU_ICON,
			'menu_position' => static::MENU_POSITION,
			'supports' => ['title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'revisions'],
			'show_in_rest' => true,
			'rest_base' => static::REST_API_ENDPOINT_SLUG,
		];
	}
}
