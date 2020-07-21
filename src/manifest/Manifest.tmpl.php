<?php
/**
 * File containing an abstract class for holding Assets Manifest functionality.
 *
 * It is used to provide manifest.json file location used with Webpack to fetch correct file locations.
 *
 * @package EightshiftLibs\Manifest
 */

declare( strict_types=1 );

namespace EightshiftBoilerplate\Manifest;

use EightshiftBoilerplateVendor\EightshiftLibs\Manifest\AbstractManifest;
use EightshiftBoilerplateVendor\EightshiftLibs\Manifest\ConfigDataInterface;

/**
 * Abstract class Manifest class.
 */
class Manifest extends AbstractManifest {

  /**
   * Instance variable of project config data.
   *
   * @var ConfigDataInterface
   */
  protected $config;

  /**
   * Create a new instance that injects config data to get project specific details.
   *
   * @param ConfigDataInterface $config Inject config which holds data regarding project details.
   */
  public function __construct( ConfigDataInterface $config ) {
    $this->config = $config;
  }
}
