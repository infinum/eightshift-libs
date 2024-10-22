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
ThemeOptions::renderPartial(ThemeOptions::getOption('header'));
?>

<main class="main-content layout-base" id="main-content">
