<?php

/**
 * Helpers for label generator.
 *
 * @package EightshiftLibs\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Helpers;

use EightshiftLibs\Exception\InvalidNouns;

/**
 * Class LabelGeneratorTrait Helper
 */
trait LabelGeneratorTrait
{
	/**
	 * Get automatically generated labels from a singular and an optional
	 * plural noun.
	 *
	 * @param array<string> $nouns Array of nouns to use for the labels.
	 *
	 * @return string[] array Array of labels.
	 * @throws InvalidNouns Invalid nouns exception.
	 */
	protected function getGeneratedLabels(array $nouns): array
	{
		$requiredNouns = [
			'upper case singular name',
			'lower case singular name',
			'upper case plural name',
			'lower case plural name',
		];

		if (\count($nouns) !== \count($requiredNouns)) {
			throw InvalidNouns::fromKey($requiredNouns[\count($nouns)]);
		}

		$labelTemplates = [
			/* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
			'name' => esc_html_x('%3$s', 'Post Type General Name', 'eightshift-libs'), /* phpcs:disable */
			/* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
			'singular_name' => esc_html_x('%1$s', 'Post Type Singular Name', 'eightshift-libs'), /* phpcs:disable */
			/* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
			'menu_name' => \esc_html__('%3$s', 'eightshift-libs'), /* phpcs:disable */
			/* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
			'name_admin_bar' => \esc_html__('%1$s', 'eightshift-libs'), /* phpcs:disable */
			/* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
			'archives' => \esc_html__('%1$s Archives', 'eightshift-libs'),
			/* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
			'attributes' => \esc_html__('%1$s Attributes', 'eightshift-libs'),
			/* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
			'parent_item_colon' => \esc_html__('Parent %1$s:', 'eightshift-libs'),
			/* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
			'all_items' => \esc_html__('All %3$s', 'eightshift-libs'),
			/* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
			'add_new_item' => \esc_html__('Add New %1$s', 'eightshift-libs'),
			/* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
			'add_new' => \esc_html__('Add New', 'eightshift-libs'),
			/* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
			'new_item' => \esc_html__('New %1$s', 'eightshift-libs'),
			/* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
			'edit_item' => \esc_html__('Edit %1$s', 'eightshift-libs'),
			/* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
			'update_item' => \esc_html__('Update %1$s', 'eightshift-libs'),
			/* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
			'view_item' => \esc_html__('View %1$s', 'eightshift-libs'),
			/* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
			'view_items' => \esc_html__('View %3$s', 'eightshift-libs'),
			/* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
			'search_items' => \esc_html__('Search %1$s', 'eightshift-libs'),
			/* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
			'not_found' => \esc_html__('Not found', 'eightshift-libs'),
			/* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
			'not_found_in_trash' => \esc_html__('Not found in Trash', 'eightshift-libs'),
			/* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
			'featured_image' => \esc_html__('Featured Image', 'eightshift-libs'),
			/* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
			'set_featured_image' => \esc_html__('Set featured image', 'eightshift-libs'),
			/* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
			'remove_featured_image' => \esc_html__('Remove featured image', 'eightshift-libs'),
			/* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
			'use_featured_image' => \esc_html__('Use as featured image', 'eightshift-libs'),
			/* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
			'insert_into_item' => \esc_html__('Insert into %2$s', 'eightshift-libs'),
			/* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
			'uploaded_to_this_item' => \esc_html__('Uploaded to this %2$s', 'eightshift-libs'),
			/* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
			'items_list' => \esc_html__('%3$s list', 'eightshift-libs'),
			/* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
			'items_list_navigation' => \esc_html__('%3$s list navigation', 'eightshift-libs'),
			/* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
			'filter_items_list' => \esc_html__('Filter %4$s list', 'eightshift-libs'),
			/* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
			'item_link' => \esc_html__('%1$s Link', 'eightshift-libs'),
			/* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
			'item_link_description' => \esc_html__('A link to a %2$s', 'eightshift-libs'),
			'filter_by_date' => \esc_html__('Filter by date', 'eightshift-libs'),
		];

		return \array_map(
			static function ($label) use ($nouns) {
				return \sprintf(
					$label,
					...$nouns
				);
			},
			$labelTemplates
		);
	}
}
