<?php

namespace Tests\Unit\Helpers;

use Brain\Monkey\Functions;
use EightshiftLibs\Helpers\Components;

/**
 * Setup before each test.
 */
beforeEach(function() {
  Functions\when('parse_blocks')
    ->alias(function($blocks) {
      return $blocks;
    });

  Functions\when('wp_kses_post')
    ->alias(function($block) {
      return $block;
    });

  Functions\when('render_block')
    ->alias(function($block) {
      $renderedBlock = '';
      foreach($block as $blockContent) {
        $renderedBlock = $renderedBlock . "<div>" . $blockContent . "</div>";
      }

      return $renderedBlock;
    });

  Functions\when('wp_strip_all_tags')
    ->alias('strip_tags');
});

/**
 * Cleanup after each test.
 */
/**
 * Checking if reading time is correct based on the content that appears in
 * dataset postsDifferentLength.
 */
test('Correct get reading time function', function ($posts) {
  Functions\when('get_the_content')
    ->alias(function($more_link_text=null, $strip_teaser=false, $postId=null) use ($posts) {
      return $posts[$postId];
    });

  foreach ($posts as $postId => $postContent) {
    $methodReadingTime = Components::getReadingTime($postId);

    $wordCount = 0;
    foreach($postContent[0] as $block => $blockContent) {
      $wordCount = $wordCount + \str_word_count($blockContent);
    }

    $testReadingTime = (int) ceil( $wordCount / 200);

    $this->assertSame($methodReadingTime, $testReadingTime);
  }

})->with('postsDifferentLength');
