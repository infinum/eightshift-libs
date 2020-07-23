<?php
/**
 * File containing an abstract class for holding Assets Manifest functionality.
 *
 * It is used to provide manifest.json file location used with Webpack to fetch correct file locations.
 *
 * @package EightshiftLibs\Manifest
 */

declare( strict_types=1 );

namespace EightshiftLibs\Manifest;

use EightshiftLibs\Config\ConfigInterface;
use EightshiftLibs\Manifest\AbstractManifest;

/**
 * Abstract class Manifest class.
 */
class Manifest extends AbstractManifest {

  /**
   * Instance variable of project config data.
   *
   * @var ConfigInterface
   */
  protected $config;

  /**
   * Create a new instance that injects config data to get project specific details.
   *
   * @param ConfigInterface $config Inject config which holds data regarding project details.
   */
  public function __construct( ConfigInterface $config ) {
    $this->config = $config;
  }
}
