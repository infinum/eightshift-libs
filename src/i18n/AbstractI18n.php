<?php
/**
 * The Language specific functionality.
 *
 * @package EightshiftLibs\I18n
 */

declare( strict_types=1 );

namespace EightshiftLibs\I18n;

use EightshiftLibs\Config\ConfigInterface;
use EightshiftLibs\Services\ServiceInterface;

/**
 * Class i18n
 *
 * This class handles theme or admin languages.
 */
abstract class AbstractI18n implements ServiceInterface {

  /**
   * Instance variable of project config data.
   *
   * @var ConfigInterface
   */
  protected $config;

  /**
   * Load the plugin text domain for translation.
   *
   * @return void
   */
  public function load_theme_textdomain() {
    \load_theme_textdomain(
      $this->config->get_project_name(),
      $this->config->get_project_path( '/src/i18n' )
    );
  }
}
