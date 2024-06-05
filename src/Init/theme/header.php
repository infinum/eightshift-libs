<?php

/**
 * Main header file
 *
 * @package %g_namespace%
 */

use %g_use_libs%\Helpers\Helpers;
use %g_namespace%\AdminMenus\ReusableBlocksHeaderFooter;

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<?php
	// Head component.
	echo Helpers::render('head', [
		'icon' => Helpers::getAsset('logo.svg'),
	]);

	wp_head();
	?>
</head>
<body <?php body_class(); ?>>

<?php
// Header reusable block.
$headerPartialId = get_option(ReusableBlocksHeaderFooter::HEADER_PARTIAL) ?? '';
ReusableBlocksHeaderFooter::renderPartial($headerPartialId);
?>

<main class="main-content" id="main-content">
