<?php
/**
 * The Theme/Frontend Enqueue specific functionality.
 *
 * @package EightshiftLibs\Enqueue\Theme
 */

declare( strict_types=1 );

namespace EightshiftLibs\Enqueue\Theme;

use EightshiftLibs\Enqueue\Theme\AbstractEnqueueTheme;
use EightshiftLibs\Manifest\ManifestInterface;

/**
 * Class EnqueueThemeExample
 */
class EnqueueThemeExample extends AbstractEnqueueTheme {

  /**
   * Create a new admin instance.
   *
   * @param ManifestInterface $manifest Inject manifest which holds data about assets from manifest.json.
   */
  public function __construct( ManifestInterface $manifest ) {
    $this->manifest = $manifest;
  }

  /**
   * Register all the hooks
   *
   * @return void
   */
  public function register() {
    add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ], 10 );
    add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
  }
}
