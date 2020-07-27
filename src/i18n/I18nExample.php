<?php
/**
 * The Language specific functionality.
 *
 * @package EightshiftLibs\I18n
 */

declare( strict_types=1 );

namespace EightshiftLibs\I18n;

use EightshiftLibs\Config\Config;
use EightshiftLibs\I18n\AbstractI18n;

/**
 * Class i18n
 *
 * This class handles theme or admin languages.
 */
class I18n extends AbstractI18n {

  /**
   * Register all the hooks
   *
   * @return void
   */
  public function register() {
    add_action( 'after_setup_theme', [ $this, 'load_theme_textdomain' ] );
  }

  /**
   * Text domain. Unique identifier for retrieving translated strings.
   *
   * @return string
   */
  public function get_textdomain_name() : string {
    return Config::get_project_name();
  }

  /**
   * Path to the directory containing the .mo file.
   *
   * @return string
   */
  public function get_translation_file_path() : string {
    return Config::get_project_path( '/src/i18n' );
  }
}
