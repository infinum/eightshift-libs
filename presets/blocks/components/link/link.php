<?php
/**
 * Template for the Link Component.
 *
 * @since 1.0.0
 * @package Eightshift_Boilerplate\Blocks.
 */

namespace Eightshift_Boilerplate\Blocks;

$title = $attributes['title'] ?? '';
$url   = $attributes['url'] ?? '';

$component_class = 'link';
$block_class     = $attributes['blockClass'] ?? '';
$style_color     = $attributes['styleColor'] ?? '';

$link_class = "
  {$component_class}
  {$component_class}__color--{$style_color}
  {$block_class}__link
";
?>

<a
  href="<?php echo esc_url( $url ); ?>"
  class="<?php echo esc_attr( $link_class ); ?>"
  title="<?php echo esc_attr( $title ); ?>"
>
  <?php echo esc_html( $title ); ?>
</a>
