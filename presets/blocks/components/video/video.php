<?php
/**
 * Template for the Video Component.
 *
 * @since 1.0.0
 * @package Eightshift_Boilerplate\Blocks.
 */

namespace Eightshift_Boilerplate\Blocks;

$url = $attributes['url'] ?? '';

$component_class = 'video';
$block_class     = $attributes['blockClass'] ?? '';

$video_class = "
  {$component_class}
  {$block_class}__video
";

?>

<video class="<?php echo esc_attr( $video_class ); ?>" autoplay loop muted>
  <source src="<?php echo esc_url( $url ); ?>" type="video/mp4">
</video>
