<?php
/**
 * File containing the main intro class for your project.
 *
 * @package EightshiftLibs\Main
 */

declare( strict_types=1 );

namespace EightshiftLibs\Main;

use DI\Container;
use DI\ContainerBuilder;
use DI\Definition\Helper\AutowireDefinitionHelper;
use DI\Definition\Reference;
use EightshiftLibs\Services\RegistrableInterface;
use EightshiftLibs\Services\ServiceInterface;

/**
 * The main start class.
 * This is used to define instantiate all classes used in the lib.
 */
abstract class AbstractMain implements ServiceInterface {

  /**
   * Array of instantiated services.
   *
   * @var Service[]
   */
  private $services = [];

  /**
   * DI container instance.
   *
   * @var Container
   */
  private $container;

  /**
   * Constructs object and injects autowiring.
   *
   * @param ClassLoader $autowiring Autowiring functionality.
   */
  public function __construct( $autowiring ) {
    $this->autowiring = $autowiring;
  }

  /**
   * Register the individual services with optional dependency injection.
   *
   * @throws Exception\Invalid_Service If a service is not valid.
   *
   * @return void
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
        if ( ! $class instanceof RegistrableInterface ) {
          return;
        }

        $class->register();
      }
    );
  }

  /**
   * Returns the DI container and allow it to be used in different context (for example in tests outside of WP environment)
   *
   * @return Container
   * @throws \Exception Exception thrown by the DI container.
   */
  public function build_di_container() {
    if ( empty( $this->container ) ) {
      $this->container = $this->get_di_container( $this->get_service_classes_prepared_array() );
    }
    return $this->container;
  }

  /**
   * Merges the autowired definition list with custom user-defined definition list. You can override
   * autowired definition lists in $this->get_service_classes().
   *
   * @return array<array>
   */
  private function get_service_classes_with_autowire() {
    return array_merge( $this->autowiring->buildServiceClasses(), $this->get_service_classes() );
  }

  /**
   * Return array of services with Dependency Injection parameters.
   *
   * @return array
   *
   * @throws \Exception Exception thrown by the DI container.
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
   */
  private function get_service_classes_prepared_array() : array {
    $output = [];

    foreach ( $this->get_service_classes_with_autowire() as $class => $dependencies ) {
      if ( is_array( $dependencies ) ) {
        $output[ $class ] = $dependencies;
        continue;
      }

      $output[ $dependencies ] = [];
    }

    return $output;
  }

  /**
   * Implement PHP-DI.
   * Build and return a DI container.
   * Wire all the dependencies automatically, based on the provided array of class => dependencies from the get_di_items().
   *
   * @param array $services Array of service.
   * @return Container
   *
   * @throws \Exception Exception thrown by the DI container.
   */
  private function get_di_container( array $services ) {
    $definitions = [];

    foreach ( $services as  $service_key => $service_values ) {
      if ( gettype( $service_values ) !== 'array' ) {
        continue;
      }

      $autowire = new AutowireDefinitionHelper();

      $definitions[ $service_key ] = $autowire->constructor( ...$this->get_di_dependencies( $service_values ) );
    }

    $builder = new ContainerBuilder();

    return $builder->addDefinitions( $definitions )->build();
  }

  /**
   * Return prepared Dependency Injection objects.
   * If you pass a class use PHP-DI to prepare if not just output it.
   *
   * @param array $dependencies Array of classes/parameters to push in constructor.
   *
   * @return array
   */
  private function get_di_dependencies( array $dependencies ) : array {
    return array_map(
      function( $dependency ) {
        if ( class_exists( $dependency ) ) {
          return new Reference( $dependency );
        }
        return $dependency;
      },
      $dependencies
    );
  }

  /**
   * Get the list of services to register.
   *
   * A list of classes which contain hooks.
   *
   * @return array<class-string, string|string[]> Array of fully qualified service class names.
   */
  abstract protected function get_service_classes() : array;
}
