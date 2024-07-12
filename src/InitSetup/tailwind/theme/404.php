<?php

/**
 * 404 error page
 *
 * @package %g_namespace%
 */

use %g_namespace%\ThemeOptions\ThemeOptions;

get_header();

// Header reusable block.
$partialId = get_option(ThemeOptions::OPTION_NAME)['fourOhFour'] ?? '';
ThemeOptions::renderPartial($partialId);

get_footer();
