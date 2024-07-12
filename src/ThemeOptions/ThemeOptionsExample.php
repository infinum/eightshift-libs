<?php

/**
 * Class that adds ThemeOptionsExample capability.
 *
 * @package %g_namespace%\ThemeOptions
 */

declare(strict_types=1);

namespace %g_namespace%\ThemeOptions;

use %g_use_libs%\Helpers\Helpers;
use %g_use_libs%\Services\ServiceInterface;
use WP_Query;

/**
 * Class ThemeOptionsExample
 */
class ThemeOptionsExample implements ServiceInterface
{
	/**
	 * Option name.
	 *
	 * @var string
	 */
	public const OPTION_NAME = 'eightshift-theme-options';

	/**
	 * Register all the hooks.
	 *
	 * @return void
	 */
	public function register(): void
	{
		\add_action('init', [$this, 'addRbHfSettings']);
	}

	/**
	 * Method that registers settings for the header/footer picker.
	 *
	 * @return void
	 */
	public function addRbHfSettings(): void
	{
		$default = [
			'header' => null,
			'footer' => null,
		];

		$schema = [
			'type' => 'object',
			'properties' => [
				'header' => [
					'type' => 'string',
				],
				'footer' => [
					'type' => 'string',
				],
				'fourOhFour' => [
					'type' => 'string',
				],
			],
		];

		\register_setting(
			'options',
			self::OPTION_NAME,
			[
				'type' => 'object',
				'default' => $default,
				'show_in_rest' => [
					'schema' => $schema,
				],
			]
		);
	}

	// Supporting functions for defining settings and sections.
	/**
	 * Renders a reusable block partial.
	 *
	 * @param int|string $partialId Block partial ID.
	 * @return void
	 */
	public static function renderPartial($partialId): void
	{
		if (empty((string) $partialId)) {
			return;
		}

		$blocksToRender = \parse_blocks(\get_the_content(null, false, $partialId));

		// Filter out empty blocks.
		$blocksToRenderFiltered = \array_values(
			\array_filter(
				$blocksToRender,
				static function ($blockArray) {
					return !empty($blockArray['blockName']);
				}
			)
		);

		$blocksToRenderRendered = \array_map(
			static function ($block) {
				return \render_block($block);
			},
			$blocksToRenderFiltered // phpcs:ignore
		);

		echo Helpers::ensureString($blocksToRenderRendered); // phpcs:ignore
	}

	/**
	 * Get reusable blocks patterns.
	 *
	 * @return array<mixed> Available patterns.
	 */
	public static function getPatterns(): array
	{
		$reusableBlocksQuery = new WP_Query([
			'post_type' => 'wp_block',
			'posts_per_page' => 1000, // phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_posts_per_page
			'post_status' => 'publish',
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		]);

		$patterns = [];

		if ($reusableBlocksQuery->have_posts()) {
			while ($reusableBlocksQuery->have_posts()) {
				$reusableBlocksQuery->the_post();
				$postId = (string) \get_the_ID();
				$postTitle = \get_the_title();

				$patterns[] = [
					'value' => $postId,
					'label' => $postTitle,
				];
			}

			\wp_reset_postdata();
		}

		return $patterns;
	}
}
