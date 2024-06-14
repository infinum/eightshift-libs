<?php

/**
 * Optimization for the project.
 *
 * @package %g_namespace%\Optimization
 */

declare(strict_types=1);

namespace %g_namespace%\Optimization;

use %g_use_libs%\Services\ServiceInterface;

/**
 * Class that modifies some administrator appearance
 *
 * Example: Change color based on environment, remove dashboard widgets etc.
 */
class OptimizationExample implements ServiceInterface
{
	/**
	 * Register all the hooks
	 *
	 * @return void
	 */
	public function register(): void
	{
		\add_action('wp_enqueue_scripts', [$this, 'dequeueStyles'], 100);
		\add_action('wp_enqueue_scripts', [$this, 'dequeueScripts'], 100);

		\remove_action('wp_head', 'print_emoji_detection_script', 7);
	}

	/**
	 * Method that removes all unnecessary styles.
	 *
	 * @return void
	 */
	public function dequeueStyles(): void
	{
		\wp_dequeue_style('wp-block-library');
		\wp_dequeue_style('wp-block-library-theme');
		\wp_dequeue_style('wc-blocks-style');
		\wp_dequeue_style('classic-theme-styles');

		if (!\is_user_logged_in()) {
			\wp_dequeue_style('wpml-blocks');
			\wp_dequeue_style('wpml-legacy-horizontal-list-0');
		}
	}

	/**
	 * Method that removes all unnecessary scripts.
	 *
	 * @return void
	 */
	public function dequeueScripts(): void
	{
		if (!\function_exists('is_plugin_active')) {
			include_once(\ABSPATH . 'wp-admin/includes/plugin.php');
		}

		// Query monitor expects jquery to work.
		if (\is_plugin_active('query-monitor/query-monitor.php') && \is_user_logged_in()) {
			return;
		}

		// Remove jquery.
		\wp_deregister_script('jquery');
		\wp_deregister_script('jquery-migrate');
	}
}
