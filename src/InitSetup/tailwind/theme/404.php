<?php

/**
 * 404 error page
 *
 * @package %g_namespace%
 */

use %g_namespace%\ThemeOptions\ThemeOptions;

get_header();

// Header reusable block.
ThemeOptions::renderPartial(ThemeOptions::getOption('fourOhFour'));

get_footer();
