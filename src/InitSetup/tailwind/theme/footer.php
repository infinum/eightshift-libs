<?php

/**
 * Display footer.
 *
 * @package %g_namespace%
 */

use %g_namespace%\ThemeOptions\ThemeOptions;

?>

</main>

<footer class="layout-base">
<?php
// Footer reusable block.
$footerPartialId = json_decode(get_option(ThemeOptions::OPTION_NAME), true)['footer'] ?? '';
ThemeOptions::renderPartial($footerPartialId);
?>
</footer>

<?php
wp_footer();
?>
</body>
</html>
