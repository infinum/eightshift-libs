<?php

/**
 * The Media specific functionality.
 *
 * @package %g_namespace%\Media
 */

declare(strict_types=1);

namespace %g_namespace%\Media;

use %g_use_libs%\Media\AbstractMedia;

/**
 * Class MediaExample
 *
 * This class handles all media options. Sizes, Types, Features, etc.
 */
class MediaExample extends AbstractMedia
{
	/**
	 * Register all the hooks
	 *
	 * @return void
	 */
	public function register(): void
	{
		\add_action('after_setup_theme', [$this, 'addThemeSupport'], 20);
		\add_filter('upload_mimes', [$this, 'enableMimeTypes']);
		\add_filter('wp_prepare_attachment_for_js', [$this, 'enableSvgMediaLibraryPreview'], 10, 2);
		\add_filter('wp_handle_upload_prefilter', [$this, 'validateSvgOnUpload']);
		\add_filter('wp_check_filetype_and_ext', [$this, 'enableSvgUpload'], 10, 3);
		\add_filter('wp_check_filetype_and_ext', [$this, 'enableJsonUpload'], 10, 3);

		// WebP.
		if (\extension_loaded('gd')) {
			\add_filter('wp_generate_attachment_metadata', [$this, 'generateWebPMedia'], 10, 2);
			\add_filter('wp_update_attachment_metadata', [$this, 'generateWebPMedia'], 10, 2);
			\add_action('delete_attachment', [$this, 'deleteWebPMedia']);
		}
	}


	/**
	 * Enable theme support
	 *
	 * For full list check: https://developer.wordpress.org/reference/functions/add_theme_support/
	 *
	 * @return void
	 */
	public function addThemeSupport(): void
	{
		\add_theme_support('title-tag');
		\add_theme_support('html5', ['style', 'script']);
		// Enables HTML5 markup support and explicitly states support for script and style tags, so WP doesn't insert the type attribute on those tags.
		// Registering the type attribute is not compliant with the HTML5 specification.
		\add_theme_support('post-thumbnails');
	}
}
