<?php
/**
 * The file that defines the autowiring process
 *
 * @package EightshiftLibs\Main
 */

declare( strict_types=1 );

namespace EightshiftLibs\Main;

/**
 * The file that defines the autowiring process
 */
class Autowiring {

  /**
   * Array of psr-4 prefixes. Should be provided by Composer's ClassLoader. $ClassLoader->getPsr4_prefixes().
   *
   * @var array
   */
  protected $psr4_prefixes;

  /**
   * Project namespace
   *
   * @var string
   */
  protected $namespace;

  /**
   * Constructs object and inserts prefixes from composer.
   *
   * @param array  $psr4_prefixes Composer's ClassLoader psr4_prefixes. $ClassLoader->getPsr4_prefixes().
   * @param string $namespace    Projects namespace.
   */
  public function __construct( array $psr4_prefixes, string $namespace ) {
    $this->psr4_prefixes = $psr4_prefixes;
    $this->namespace     = $namespace;
  }

  /**
   * Autowiring.
   *
   * @return array<array> Array of fully qualified class names.
   */
  public function build_service_classes() : array {
    $project_classes = $this->get_classes_in_namespace( $this->namespace, $this->psr4_prefixes );

    $dependency_tree = [];
    $filename_index  = [];

    // Prepare the filename index.
    $filename_index        = $this->build_filename_index( $project_classes );
    $class_interface_index = $this->build_class_interface_index( $project_classes );

    foreach ( $project_classes as $project_class ) {
      $refl_class = new \ReflectionClass( $project_class );

      // Skip abstract classes, interfaces & traits.
      if ( $refl_class->isAbstract() || $refl_class->isInterface() || $refl_class->isTrait() ) {
        continue;
      }

      // Skip irrelevant classes.
      if (
        ! $this->is_service_class( $refl_class->getInterfaceNames() )
        && ( empty( $refl_class->getConstructor() ) || empty( $refl_class->getConstructor()->getParameters() ) )
      ) {
        continue;
      }

      // Build the dependency tree.
      $dependency_tree = array_merge( $this->build_dependency_tree( $project_class, $filename_index, $class_interface_index ), $dependency_tree );
    }

    // Build dependency tree for dependencies. Things that need to be injected but were skipped because
    // they were initially irrelevant.
    foreach ( $dependency_tree as $dependencies ) {
      foreach ( $dependencies as $dep_class => $sub_deps ) {

        // No need to build dependencies for this again if we already have them.
        if ( isset( $dependency_tree[ $dep_class ] ) ) {
          continue;
        }

        $dependency_tree = array_merge( $this->build_dependency_tree( $dep_class, $filename_index, $class_interface_index ), $dependency_tree );
      }
    }

    // Convert dependency tree into PHP-DI's definition list.
    $classes = $this->convert_dependency_tree_into_definition_list( $dependency_tree );

    return $classes;
  }

  /**
   * Check if provided class is part of a service classes. Check if it contains ServiceInterface.
   *
   * @param array $interfaces List of class interfaces.
   *
   * @return boolean
   */
  protected function is_service_class( array $interfaces = [] ) : bool {
    foreach ( $interfaces as $interface ) {
      $items = explode( '\\', $interface );
      if ( end( $items ) !== 'ServiceInterface' ) {
        return true;
      }
    }

    return false;
  }

  /**
   * Builds the dependency tree for a single class ($relevant_class)
   *
   * @param  string $relevant_class       Class we're building dependency tree for.
   * @param  array  $filename_index       Filename index. Maps filenames to class names.
   * @param  array  $class_interface_index Class interface index. Maps classes to interfaces they implement.
   * @return array
   */
  protected function build_dependency_tree( string $relevant_class, array $filename_index, array $class_interface_index ) {
    $dependency_tree = [];
    $refl_class      = new \ReflectionClass( $relevant_class );

    // If this class has dependencies, we need to figure those out. Otherwise
    // we just add it to the dependency tree as a class without dependencies.
    if ( ! empty( $refl_class->getConstructor() ) ) {

      // Go through each constructor parameter.
      foreach ( $refl_class->getConstructor()->getParameters() as $refl_param ) {

        if ( $refl_param->getType() === null ) {
          continue;
        }

        $classname            = $refl_param->getType()->getName();
        $refl_class_for_param = new \ReflectionClass( $classname );

        // If the expected type is interface, try guessing based on var name. Otherwise
        // Just inject that class.
        if ( $refl_class_for_param->isInterface() ) {
          $matched_class = $this->try_to_find_matching_class( $refl_param->getName(), $classname, $filename_index, $class_interface_index );

          // If we're unable to find exactly 1 class for whatever reason, just skip it, the user
          // will have to define the dependencies manually.
          if ( empty( $matched_class ) ) {
            continue;
          }

          $dependency_tree[ $relevant_class ][ $matched_class ] = [];
        } else {
          $dependency_tree[ $relevant_class ][ $classname ] = [];
        }
      }
    } else {
      $dependency_tree[ $relevant_class ] = [];
    }

    return $dependency_tree;
  }

  /**
   * Returns all classes in namespace.
   *
   * @param  string $namespace    Name of namespace.
   * @param  array  $psr4_prefixes Array of psr-4 compliant namespaces and their accompanying folders.
   * @return array
   */
  protected function get_classes_in_namespace( string $namespace, array $psr4_prefixes ) : array {
    $classes              = [];
    $namespace_with_slash = "{$namespace}\\";
    $path_to_namespace    = $psr4_prefixes[ $namespace_with_slash ][0] ?? '';

    if ( ! is_dir( $path_to_namespace ) ) {
      return [];
    }

    $it = new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator( $path_to_namespace ) );
    foreach ( $it as $file ) {
      if ( $file->isDir() ) {
        continue;
      }
      if ( preg_match( '/\.(php)$/', $file->getFileName() ) && preg_match( '/^[A-Z]{1,}.*$/m', $file->getFileName() ) ) {
        $classes[] = $this->get_namespace_from_filepath( $file->getPathname(), $namespace, $path_to_namespace );
      }
    }

    return $classes;
  }

  /**
   * Builds PSR namespace SolplanetVendor\from file's path.
   *
   * @param  string $filepath            Path to a file.
   * @param  string $root_namespace      Root namespace SolplanetVendor\we're getting classes from.
   * @param  string $root_namespace_path Path to root namespace SolplanetVendor\.
   *
   * @return string
   */
  protected function get_namespace_from_filepath( string $filepath, string $root_namespace, string $root_namespace_path ) : string {
    return $root_namespace . str_replace(
      [ $root_namespace_path, DIRECTORY_SEPARATOR, '.php' ],
      [ '', '\\', '' ],
      $filepath
    );
  }


  /**
   * Try to uniquely match the $filename.
   *
   * @param  string $filename              Filename based on variable name.
   * @param  string $interface_name        Interface we're trying to match.
   * @param  array  $filename_index        Filename index. Maps filenames to class names.
   * @param  array  $class_interface_index Class interface index. Maps classes to interfaces they implement.
   * @return string
   *
   * @throws \Exception If things we're looking for are missing inside filename or classInterface index (which shouldn't happen).
   */
  protected function try_to_find_matching_class( string $filename, string $interface_name, array $filename_index, array $class_interface_index ) : string {

    // If there's no matches in filename index by variable, we need to skip it, this dependency's definition.
    // list need sto be build manually.
    if ( ! isset( $filename_index[ $filename ] ) ) {
      throw new \Exception( "File {$filename} not found filename_index, aborting" );
    }

    // Lets go through each file that's called $filename and check which interfaces that class
    // implements (if any).
    $matches = 0;
    foreach ( $filename_index[ $filename ] as $class_in_filename ) {
      if ( ! isset( $class_interface_index[ $class_in_filename ] ) ) {
        throw new \Exception( "Class {$class_in_filename} not found in class_interface_index, aborting." );
      }

      // If the current class implements the interface we're looking for, great! We still need to go through all other
      // classes to make sure we don't get more than 1 match.
      if ( isset( $class_interface_index[ $class_in_filename ][ $interface_name ] ) ) {
        $match = $class_in_filename;
        $matches++;
      }
    }

    // If we don't have a unique match (i.e. if 2 classes of the same name are implementing the interface we're looking for)
    // then we need to cancel the match because we don't know how to handle that.
    if ( $matches !== 1 ) {
      $match = '';
    }

    return $match;
  }

  /**
   * Builds the PSR-4 filename index. Maps filenames to class names.
   *
   * @param  array $all_relevant_classes PSR-4 Namespace prefixes, can be build this Composer's ClassLoader ( $loader->getPsr4_prefixes() ).
   * @return array
   */
  protected function build_filename_index( array $all_relevant_classes ) : array {
    $filename_index = [];
    foreach ( $all_relevant_classes as $relevant_class ) {
      $filename = $this->get_filename_from_class( $relevant_class );

      $filename_index[ $filename ][] = $relevant_class;
    }

    return $filename_index;
  }

  /**
   * Builds the PSR-4 class => [ $interfaces ] index. Maps classes to interfaces they implement.
   *
   * @param array $all_relevant_classes PSR-4 Namespace prefixes, can be build this Composer's ClassLoader ( $loader->getPsr4_prefixes() ).
   * @return array
   */
  protected function build_class_interface_index( array $all_relevant_classes ) : array {
    $class_interface_index = [];
    foreach ( $all_relevant_classes as $relevant_class ) {
      $interfaces = array_map(
        function() {
          return true;
        },
        ( new \ReflectionClass( $relevant_class ) )->getInterfaces()
      );

      $class_interface_index[ $relevant_class ] = $interfaces;
    }

    return $class_interface_index;
  }

  /**
   * Returns filename from fully-qualified class names
   *
   * Example: AutowiringTest/Something/Class => class
   *
   * @param  string $classname Fully qualified classname.
   * @return string
   */
  protected function get_filename_from_class( string $classname ) : string {
    return lcfirst( trim( substr( $classname, strrpos( $classname, '\\' ) + 1 ) ) );
  }

  /**
   * Takes the dependency tree array and convert's it into PHP-DI's definition list. Recursive.
   *
   * @param  array $dependency_tree Dependency tree.
   * @return array
   */
  protected function convert_dependency_tree_into_definition_list( array $dependency_tree ) {
    $classes = [];
    foreach ( $dependency_tree as $class_name => $dependencies ) {
      if ( empty( $dependencies ) ) {
        $classes[] = $class_name;
      } else {
        $classes[ $class_name ] = $this->convert_dependency_tree_into_definition_list( $dependencies );
      }
    }

    return $classes;
  }
}
