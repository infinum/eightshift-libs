<?php

/**
 * Single page for Posts
 *
 * @package %g_namespace%
 */

use %g_use_libs%\Helpers\Helpers;

get_header();

if (have_posts()) {
	while (have_posts()) {
		echo Helpers::render('post-header');
		the_post();
		the_content();
	}
}

get_footer();
