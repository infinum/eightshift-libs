<?php
/**
 * Main header bar
 *
 * @package Eightshift_Boilerplate\Views\Header
 *
 * @since 1.0.0
 */

use Eightshift_Boilerplate\Admin\Menu\Menu;
use Eightshift_Boilerplate\Manifest\Manifest;

$main_menu        = new Menu();
$blog_name        = get_bloginfo( 'name' );
$blog_description = get_bloginfo( 'description' );
$header_logo_info = $blog_name . ' - ' . $blog_description;
$logo_img         = apply_filters( Manifest::MANIFEST_ITEM_FILTER_NAME, 'logo.svg' );

?>
<div class="header">
  <a class="header__logo-link" href="<?php echo esc_url( home_url() ); ?>" title="<?php echo esc_attr( $blog_name ); ?>">
    <img class="header__logo-img" src="<?php echo esc_url( $logo_img ); ?>" title="<?php echo esc_attr( $header_logo_info ); ?>" alt="<?php echo esc_attr( $header_logo_info ); ?>" />
  </a>
  <?php
    echo esc_html( $main_menu->bem_menu( 'header_main_nav', 'main-navigation' ) );

    get_template_part( 'src/blocks/layout/header/components/search/search' );
  ?>
</div>
