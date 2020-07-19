<?php
/**
 * The Language specific functionality.
 *
 * @package EightshiftLibs\I18n
 */

declare( strict_types=1 );

namespace EightshiftLibs\I18n;

use EightshiftLibs\Core\ServiceInterface;
use EightshiftLibs\Core\ConfigDataInterface;

/**
 * Class i18n
 *
 * This class handles theme or admin languages.
 */
class I18n implements ServiceInterface {

  /**
   * Instance variable of project config data.
   *
   * @var ConfigDataInterface
   */
  protected $config;

  /**
   * Create a new instance.
   *
   * @param ConfigDataInterface $config Inject config which holds data regarding project details.
   */
  public function __construct( ConfigDataInterface $config ) {
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
