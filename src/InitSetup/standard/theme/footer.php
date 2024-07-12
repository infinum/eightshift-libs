<?php

/**
 * Display footer.
 *
 * @package %g_namespace%
 */

use %g_namespace%\AdminMenus\AdminPatternsHeaderFooterMenu;

?>

</main>

<?php
$footerPartialId = get_option(AdminPatternsHeaderFooterMenu::FOOTER_PARTIAL) ?? '';
AdminPatternsHeaderFooterMenu::renderPartial($footerPartialId);

wp_footer();
?>
</body>
</html>
