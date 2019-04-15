<?php
/**
 * File containing the main intro class
 *
 * @since   1.0.0
 * @package Eightshift_Libs\Core
 */

namespace Eightshift_Libs\Core;

use Eightshift_Libs\Exception;

/**
 * The main start class.
 *
 * This is used to define instantiate all classes used the lib.
 */
abstract class Main implements Service {

  /**
   * Array of instantiated services.
   *
   * @var Service[]
   *
   * @since 1.0.0
   */
  private $services = [];

  /**
   * Default main action hook that start the whole lib.
   *
   * @since 1.0.0
   */
  const DEFAULT_REGISTER_ACTION_HOOK = 'after_setup_theme';

  /**
   * Register the theme/plugin with the WordPress system.
   *
   * The register_service method will call the register() method in every service class,
   * which holds the actions and filters - effectively replacing the need to manually add
   * them in one place.
   *
   * @throws Exception\Invalid_Service If a service is not valid.
   *
   * @return void
   *
   * @since 1.0.0
   */
  public function register() : void {
    add_action( $this->get_register_action_hook(), [ $this, 'register_services' ] );
  }

  /**
   * Returns Theme/Plugin main action hook that start the whole lib.
   *
   * @return string
   *
   * @since 1.0.0
   */
  public function get_register_action_hook() : string {
    return self::DEFAULT_REGISTER_ACTION_HOOK;
  }

  /**
   * Register the individual services of this theme/plugin.
   *
   * @throws Exception\Invalid_Service If a service is not valid.
   *
   * @return void
   *
   * @since 1.0.0
   */
  public function register_services() : void {

    // Bail early so we don't instantiate services twice.
    if ( ! empty( $this->services ) ) {
      return;
    }

    $classes = $this->get_service_classes();

    $this->services = array_map(
      [ $this, 'instantiate_service' ],
      $classes
    );
    array_walk(
      $this->services,
      function( Service $service ) {
        $service->register();
      }
    );
  }

  /**
   * Instantiate a single service.
   *
   * @param string $class Service class to instantiate.
   *
   * @throws Exception\Invalid_Service If the service is not valid.
   *
   * @return Service
   *
   * @since 1.0.0
   */
  private function instantiate_service( $class ) {
    if ( ! class_exists( $class ) ) {
      throw Exception\Invalid_Service::from_service( $class );
    }

    $service = new $class();
    if ( ! $service instanceof Service ) {
      throw Exception\Invalid_Service::from_service( $service );
    }

    return $service;
  }

  /**
   * Get the list of services to register.
   *
   * A list of classes which contain hooks.
   *
   * @return array<string> Array of fully qualified class names.
   *
   * @since 1.0.0
   */
  abstract protected function get_service_classes() : array;
}
