<?php

/**
 * Display footer.
 *
 * @package %g_namespace%
 */

use %g_namespace%\AdminMenus\ReusableBlocksHeaderFooter;

?>

</main>

<?php
$footerPartialId = get_option(ReusableBlocksHeaderFooter::FOOTER_PARTIAL) ?? '';
ReusableBlocksHeaderFooter::renderPartial($footerPartialId);

wp_footer();
?>
</body>
</html>
