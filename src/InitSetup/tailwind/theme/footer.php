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
ThemeOptions::renderPartial(ThemeOptions::getOption('footer'));
?>
</footer>

<?php
wp_footer();
?>
</body>
</html>
