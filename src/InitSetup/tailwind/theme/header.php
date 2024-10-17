<?php

/**
 * Main header file
 *
 * @package %g_namespace%
 */

use %g_use_libs%\Helpers\Helpers;
use %g_namespace%\ThemeOptions\ThemeOptions;

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<?php
	// Head component.
	echo Helpers::render('head');

	wp_head();
	?>
</head>
<body <?php body_class(); ?>>

<?php
// Header reusable block.
$headerPartialId = json_decode(get_option(ThemeOptions::OPTION_NAME), true)['header'] ?? '';
ThemeOptions::renderPartial($headerPartialId);
?>

<main class="main-content layout-base" id="main-content">
