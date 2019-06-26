<?php
/**
 * File containing the main intro class
 *
 * @since   0.7.0 Dependency Injection Refactoring
 * @since   0.1.0
 * @package Eightshift_Libs\Core
 */

namespace Eightshift_Libs\Core;

use Eightshift_Libs\Exception;

use \DI\ContainerBuilder;

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
   * @since 0.1.0
   */
  private $services = [];

  /**
   * Default main action hook that start the whole lib.
   *
   * @since 0.1.0
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
   * @since 0.8.0 Removing type hinting void for php 7.0.
   * @since 0.1.0
   */
  public function register() {
    add_action( $this->get_register_action_hook(), [ $this, 'register_services' ] );
  }

  /**
   * Returns Theme/Plugin main action hook that start the whole lib.
   *
   * @return string
   *
   * @since 0.1.0
   */
  public function get_register_action_hook() : string {
    return self::DEFAULT_REGISTER_ACTION_HOOK;
  }

  /**
   * Register the individual services with optional dependency injection.
   *
   * @throws Exception\Invalid_Service If a service is not valid.
   *
   * @return void
   *
   * @since 0.7.0 Dependency Injection Refactoring
   * @since 0.1.0
   */
  public function register_services() {

    // Bail early so we don't instantiate services twice.
    if ( ! empty( $this->services ) ) {
      return;
    }

    $this->services = $this->get_service_classes_with_di();

    array_walk(
      $this->services,
      function( $class ) {
        if ( ! $class instanceof Registrable ) {
          return;
        }

        $class->register();
      }
    );
  }

  /**
   * Return array of services with Dependency Injection parameters.
   *
   * @return array
   *
   * @since 0.7.0 Init
   */
  private function get_service_classes_with_di() : array {
    $services = $this->get_service_classes_prepared_array();

    $container = $this->get_di_container( $services );

    return array_map(
      function( $class ) use ( $container ) {
        return $container->get( $class );
      },
      array_keys( $services )
    );
  }

  /**
   * Get services classes array and prepare it for dependency injection.
   * Key should be a class name, and value should be an empty array or the dependencies of the class.
   *
   * @return array
   *
   * @since 0.7.0 Init.
   */
  private function get_service_classes_prepared_array() : array {
    $output = [];
    foreach ( $this->get_service_classes() as $class => $dependencies ) {
      if ( gettype( $dependencies ) !== 'array' ) {
        $output[ $dependencies ] = [];
      } else {
        $output[ $class ] = $dependencies;
      }
    }

    return $output;
  }

  /**
   * Implement PHP-DI.
   * Build and return a DI container.
   * Wire all the dependencies automatically, based on the provided array of class => dependencies from the get_di_items().
   *
   * @param array $services Array of service.
   * @return object
   *
   * @since 0.7.0 Init.
   */
  private function get_di_container( array $services ) {
    $builder = new ContainerBuilder();

    $definitions = [];
    foreach ( $services as  $service_key => $service_values ) {
      if ( gettype( $service_values ) !== 'array' ) {
        continue;
      }

      $definitions[ $service_key ] = \DI\create()->constructor( ...$this->get_di_dependencies( $service_values ) );
    }

    return $builder->addDefinitions( $definitions )->build();
  }

  /**
   * Return prepared Dependency Injection objects.
   * If you pass a class use PHP-DI to prepare if not just output it.
   *
   * @param array $dependencies Array of classes/parameters to push in constructor.
   * @return array
   *
   * @since 0.7.0 Init
   */
  private function get_di_dependencies( array $dependencies ) : array {
    return array_map(
      function( $dependency ) {
        if ( class_exists( $dependency ) ) {
          return \DI\get( $dependency );
        }
        return $dependency;
      },
      $dependencies
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
   * @since 0.1.0
   */
  private function instantiate_service( $class ) {
    if ( ! class_exists( $class ) ) {
      throw Exception\Invalid_Service::from_service( $class );
    }

    $service = new $class();
    if ( ! $service instanceof Registrable ) {
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
   * @since 0.1.0
   */
  abstract protected function get_service_classes() : array;
}
