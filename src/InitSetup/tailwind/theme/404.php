<?php

/**
 * 404 error page
 *
 * @package %g_namespace%
 */

use %g_namespace%\ThemeOptions\ThemeOptions;

get_header();

// Header reusable block.
$partialId = json_decode(get_option(ThemeOptions::OPTION_NAME), true)['fourOhFour'] ?? '';
ThemeOptions::renderPartial($partialId);

get_footer();
