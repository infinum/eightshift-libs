<?php
/**
 * The Language specific functionality.
 *
 * @package EightshiftLibs\I18n
 */

declare( strict_types=1 );

namespace EightshiftLibs\I18n;

use EightshiftLibs\Config\ConfigInterface;
use EightshiftLibs\I18n\AbstractI18n;

/**
 * Class i18n
 *
 * This class handles theme or admin languages.
 */
class I18n extends AbstractI18n {

  /**
   * Create a new instance.
   *
   * @param ConfigInterface $config Inject config which holds data regarding project details.
   */
  public function __construct( ConfigInterface $config ) {
    $this->config = $config;
  }

  /**
   * Register all the hooks
   *
   * @return void
   */
  public function register() {
    add_action( 'after_setup_theme', [ $this, 'load_theme_textdomain' ] );
  }
}
