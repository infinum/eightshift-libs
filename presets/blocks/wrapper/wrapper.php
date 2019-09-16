<?php
/**
 * Template for the Wrapping Advance block.
 *
 * @since 1.0.0
 * @package Eightshift_Boilerplate\Blocks.
 */

namespace Eightshift_Boilerplate\Blocks;

// Used to add or remove wrapper.
$has_wrapper = $attributes['hasWrapper'] ?? true;

if ( $has_wrapper ) {

  $id = $attributes['id'] ?? '';

  $wrapper_main_class = 'wrapper';

  $background_color      = isset( $attributes['styleBackgroundColor'] ) ? "{$wrapper_main_class}__bg-color--{$attributes['styleBackgroundColor']}" : '';
  $text_color            = isset( $attributes['styleTextColor'] ) ? "{$wrapper_main_class}__text-color--{$attributes['styleTextColor']}" : '';
  $content_width         = isset( $attributes['styleContentWidth'] ) ? "{$wrapper_main_class}__width--{$attributes['styleContentWidth']}" : '';
  $spacing_top           = isset( $attributes['styleSpacingTop'] ) ? "{$wrapper_main_class}__spacing-top--{$attributes['styleSpacingTop']}" : '';
  $spacing_top_tablet    = isset( $attributes['styleSpacingTopTablet'] ) ? "{$wrapper_main_class}__spacing-top-tablet--{$attributes['styleSpacingTopTablet']}" : '';
  $spacing_top_mobile    = isset( $attributes['styleSpacingTopMobile'] ) ? "{$wrapper_main_class}__spacing-top-mobile--{$attributes['styleSpacingTopMobile']}" : '';
  $spacing_bottom        = isset( $attributes['styleSpacingBottom'] ) ? "{$wrapper_main_class}__spacing-bottom--{$attributes['styleSpacingBottom']}" : '';
  $spacing_bottom_tablet = isset( $attributes['styleSpacingBottomTablet'] ) ? "{$wrapper_main_class}__spacing-bottom-tablet--{$attributes['styleSpacingBottomTablet']}" : '';
  $spacing_bottom_mobile = isset( $attributes['styleSpacingBottomMobile'] ) ? "{$wrapper_main_class}__spacing-bottom-mobile--{$attributes['styleSpacingBottomMobile']}" : '';
  $show_only_mobile      = isset( $attributes['styleShowOnlyMobile'] ) ? "{$wrapper_main_class}__show-only-mobile--{$attributes['styleShowOnlyMobile']}" : '';

  $wrapper_class = "
    {$wrapper_main_class}
    {$background_color}
    {$text_color}
    {$content_width}
    {$spacing_top}
    {$spacing_top_tablet}
    {$spacing_top_mobile}
    {$spacing_bottom}
    {$spacing_bottom_tablet}
    {$spacing_bottom_mobile}
    {$show_only_mobile}
  ";

  $container_width   = isset( $attributes['styleContainerWidth'] ) ? "{$wrapper_main_class}__container-width--{$attributes['styleContainerWidth']}" : '';
  $container_spacing = isset( $attributes['styleContainerSpacing'] ) ? "{$wrapper_main_class}__container-spacing--{$attributes['styleContainerSpacing']}" : '';

  $wrapper_container_class = "
    {$wrapper_main_class}__container
    {$container_width}
    {$container_spacing}
  ";

  $content_offset = isset( $attributes['styleContentOffset'] ) ? "{$wrapper_main_class}__inner-offset--{$attributes['styleContentOffset']}" : '';

  $wrapper_inner_class = "
    {$wrapper_main_class}__inner
    {$content_offset}
  ";

  ?>
  <div class="<?php echo esc_attr( $wrapper_class ); ?>" id="<?php echo esc_attr( $id ); ?>">
    <div class="<?php echo esc_attr( $wrapper_container_class ); ?>">
      <div class="<?php echo esc_attr( $wrapper_inner_class ); ?>">
        <?php
          $this->render_wrapper_view(
            $template_path,
            $attributes,
            $inner_block_content
          );
        ?>
      </div>
    </div>
  </div>
  <?php
} else {
  $this->render_wrapper_view(
    $template_path,
    $attributes,
    $inner_block_content
  );
}
